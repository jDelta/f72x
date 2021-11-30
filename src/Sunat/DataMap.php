<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 *
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat;

use DateTime;
use InvalidArgumentException;
use F72X\Company;
use F72X\Sunat\Catalogo;
use F72X\Sunat\DocumentGenerator;

/**
 * DataMap
 *
 * Esta clase es una representación interna de una factura o boleta, la cual se
 * encarga de aplicar toda la logica de negocio en cuanto a cálculos de tributos
 * y totales
 *
 */
class DataMap
{

    private $_rawData;
    private $documentType;
    private $currencyCode;
    private $documentId;
    private $officialDocumentName;
    private $documentSeries;
    private $documentNumber;

    /** @var DateTime */
    private $issueDate;

    /** @var DateTime */
    private $dueDate;
    private $operationType;
    private $customerDocType;
    private $customerDocNumber;
    private $customerRegName;
    private $customerAddress;
    private $purchaseOrder;

    private $formOfPayment;
    private $installments = [];
    private $pendingAmount = 0;

    /** @var InvoiceItems */
    private $_items;
    private $_rawItems;
    private $allowancesAndCharges = [];

    /** NOTES FIELDS */
    private $note;
    private $noteType;
    private $discrepancyResponseReason;
    private $noteAffectedDocType;
    private $noteAffectedDocId;
    /**
     *
     * @param array $data
     * @param string $type 01|03|07|08 The document type
     */
    public function __construct(array $data, $type)
    {
        // Type validation
        if (!in_array($type, ['01', '03', '07', '08'])) {
            throw new InvalidArgumentException("DataMap Error, el tipo de documento '$type', no es válido use 01|03|07|08");
        }

        // Items
        $items = new InvoiceItems();
        $items->populate($data['items'], $data['currencyCode']);

        $this->_rawData  = $data;
        $this->_rawItems = $data['items'];
        $this->setDefaults($data);
        $this->currencyCode         = $data['currencyCode'];
        $this->documentType         = $type;
        $this->documentSeries       = DocumentGenerator::buildDocumentSeries($type, $data['affectedDocType'], $data['documentSeries']);
        $this->documentNumber       = str_pad($data['documentNumber'], 8, '0', STR_PAD_LEFT);
        $this->officialDocumentName = Catalogo::getOfficialDocumentName($type);
        $this->documentId           = $this->documentSeries . '-' . $this->documentNumber;
        $this->issueDate            = new DateTime($data['issueDate']);
        if (isset($data['dueDate'])) {
            $this->dueDate            = new DateTime($data['dueDate']);
        }
        $this->customerDocType      = $data['customerDocType'];
        $this->customerDocNumber    = $data['customerDocNumber'];
        $this->customerRegName      = mb_strtoupper($data['customerRegName']);
        $this->customerAddress      = mb_strtoupper($data['customerAddress']);
        $this->_items               = $items;
        $this->allowancesAndCharges = $data['allowancesCharges'];
        // Facturas y notas de crédito deben establecer explicitamnte la forma de pago.
        if ($type == Catalogo::DOCTYPE_FACTURA || $type == Catalogo::DOCTYPE_NOTA_CREDITO) {
            $this->setFormOfPaymentField($data);
        }
        // Note
        $this->note = $data['note'];
        $this->setSpecificFields($data, $type);
    }
    private function setFormOfPaymentField(&$data)
    {
        if (!isset($data['formOfPayment'])) {
            throw new InvalidArgumentException("El campo 'formOfPayment', es obligatorio para facturas y notas de crédito");
        }
        $formOfPayment = $data['formOfPayment'];
        if (!in_array($formOfPayment, Catalogo::$FAC_FORMS_OF_PAYMENT)) {
            throw new InvalidArgumentException("El campo 'formOfPayment', no es válido use " . implode(', ', Catalogo::$FAC_FORMS_OF_PAYMENT));
        }
        $this->formOfPayment = $formOfPayment;
        // Caso crédito
        if ($formOfPayment == Catalogo::FAC_FORM_OF_PAYMENT_CREDITO) {
            if (!isset($data['pendingAmount']) || is_nan($data['pendingAmount']) || $data['pendingAmount'] <= 0) {
                throw new InvalidArgumentException("El campo 'pendingAmount', es obligatorio y debe ser mayor que cero para facturas y notas de crédito con forma de pago '" . Catalogo::FAC_FORM_OF_PAYMENT_CREDITO . "'");
            }
            if (!isset($data['installments']) || !is_array($data['installments']) || count($data['installments']) == 0) {
                throw new InvalidArgumentException("El campo 'installments', es obligatorio para facturas y notas de crédito con forma de pago '" . Catalogo::FAC_FORM_OF_PAYMENT_CREDITO . "'");
            }
            $this->pendingAmount = $data['pendingAmount'];
            $this->installments = $this->parseInstallments($data['installments']);
        }
    }
    private function parseInstallments($data)
    {
        $out = [];
        foreach ($data as $key => $item) {
            $inst = new CreditInstallment();
            $id = "Cuota" . str_pad($key + 1, 3, '0', STR_PAD_LEFT);
            $inst
                ->setId($id)
                ->setAmount($item['amount'])
                ->setPaymentDueDate(new DateTime($item['paymentDueDate']));
            $out[] = $inst;
        }
        return $out;
    }

    private function setSpecificFields(array $data, $type)
    {
        if (in_array($type, [Catalogo::DOCTYPE_FACTURA, Catalogo::DOCTYPE_BOLETA])) {
            $this->operationType = $data['operationType'];
            $this->purchaseOrder = $data['purchaseOrder'];
        } else {
            // Catálogo 9 tipo de nota de crédito, 10 tipo de nota de débito
            $catNumber = ($type == Catalogo::DOCTYPE_NOTA_CREDITO ? 9 : 10);
            $this->discrepancyResponseReason = Catalogo::getCatItemValue($catNumber, $data['noteType']);
            $this->noteType            = $data['noteType'];
            $this->noteAffectedDocType = $data['affectedDocType'];
            $this->noteAffectedDocId   = $data['affectedDocId'];
        }
    }

    private function setDefaults(array &$data)
    {
        $data['allowancesCharges'] = isset($data['allowancesCharges']) ? $data['allowancesCharges'] : [];
        $data['purchaseOrder'] = isset($data['purchaseOrder']) ? $data['purchaseOrder'] : null;
        $data['affectedDocType'] = isset($data['affectedDocType']) ? $data['affectedDocType'] : null;
        $data['note'] = isset($data['note']) ? $data['note'] : '';
    }
    public function getNoteType()
    {
        return $this->noteType;
    }

    public function getDiscrepancyResponseReason()
    {
        return $this->discrepancyResponseReason;
    }

    public function getNoteDescription()
    {
        return $this->note;
    }
    public function getNote()
    {
        return $this->note;
    }
    public function getNoteAffectedDocType()
    {
        return $this->noteAffectedDocType;
    }

    public function getNoteAffectedDocId()
    {
        return $this->noteAffectedDocId;
    }

    public function getDocumentId()
    {
        return $this->documentId;
    }

    public function getRawData()
    {
        return $this->_rawData;
    }

    /**
     * Boleta o Factura @CAT1
     * @return string
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    public function getOfficialDocumentName()
    {
        return $this->officialDocumentName;
    }

    public function getDocumentSeries()
    {
        return $this->documentSeries;
    }

    public function setDocumentSeries($documentSeries)
    {
        $this->documentSeries = $documentSeries;
        return $this;
    }

    public function getDocumentNumber()
    {
        return $this->documentNumber;
    }

    public function setDocumentNumber($documentNumber)
    {
        $this->documentNumber = $documentNumber;
        return $this;
    }

    public function getIssueDate()
    {
        return $this->issueDate;
    }

    public function setIssueDate(DateTime $IssueDate)
    {
        $this->issueDate = $IssueDate;
        return $this;
    }

    public function getDueDate()
    {
        return $this->dueDate;
    }

    public function setDueDate(DateTime $DueDate)
    {
        $this->dueDate = $DueDate;
        return $this;
    }

    public function getOperationType()
    {
        return $this->operationType;
    }

    public function setOperationType($operationType)
    {
        $this->operationType = $operationType;
        return $this;
    }

    public function getCustomerDocType()
    {
        return $this->customerDocType;
    }

    public function setCustomerDocType($customerDocType)
    {
        $this->customerDocType = $customerDocType;
        return $this;
    }

    public function getCustomerDocNumber()
    {
        return $this->customerDocNumber;
    }

    public function setCustomerDocNumber($customerDocNumber)
    {
        $this->customerDocNumber = $customerDocNumber;
        return $this;
    }

    public function getCustomerRegName()
    {
        return $this->customerRegName;
    }

    public function setCustomerRegName($customerRegName)
    {
        $this->customerRegName = $customerRegName;
        return $this;
    }
    public function getCustomerAddress()
    {
        return $this->customerAddress;
    }

    public function setCustomerAddress($customerAddress)
    {
        $this->customerAddress = $customerAddress;
        return $this;
    }

    public function getPurchaseOrder()
    {
        return $this->purchaseOrder;
    }

    public function setPurchaseOrder($purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
        return $this;
    }

    public function getFormOfPayment()
    {
        return $this->formOfPayment;
    }

    /**
     *
     * @return InvoiceItems
     */
    public function getItems()
    {
        return $this->_items;
    }
    /**
     *
     * @return CreditInstallment[]
     */
    public function getInstallments()
    {
        return $this->installments;
    }
    public function getPendingAmount()
    {
        return $this->pendingAmount;
    }
    /**
     * Numero de items del documento
     * @return int
     */
    public function getTotalItems()
    {
        return $this->_items->getCount();
    }

    public function getRawItems()
    {
        return $this->_rawItems;
    }

    public function getAllowancesAndCharges()
    {
        return $this->allowancesAndCharges;
    }

    public function setAllowancesAndCharges($allowancesAndCharges)
    {
        $this->allowancesAndCharges = $allowancesAndCharges;
        return $this;
    }

    /**
     * Monto facturable (Valor de venta)
     *
     * Formula = SUM(BASE_FACTURABLE_X_ITEM)
     *
     * @return float
     */
    public function getBillableAmount()
    {
        return $this->_items->getTotalBillableAmount();
    }

    /**
     * Base imponible
     *
     * Formula = SUM(BASE_IMPONIBLE_X_ITEM) - DESCUENTOS_GLOBALES + CARGOS_GLOBALES
     *
     * @return float
     */
    public function getTaxableAmount()
    {
        $totalItems = $this->_items->getTotalTaxableOperations();
        return $this->applyAllowancesAndCharges($totalItems);
    }

    /**
     * Monto con impuestos
     *
     * Formula = BASE_IMPONIBLE + IGV
     *
     * @return float
     */
    public function getTaxedAmount()
    {
        $totalTaxableAmount = $this->getTaxableAmount();
        $totalIgv = $this->getIGV();
        return $totalTaxableAmount + $totalIgv;
    }

    /**
     * Total operaciones gravadas
     * @return float
     */
    public function getTotalTaxableOperations()
    {
        return $this->getTaxableAmount();
    }

    /**
     * Total operaciones gratuitas
     * @return float
     */
    public function getTotalFreeOperations()
    {
        return $this->_items->getTotalFreeOperations();
    }

    /**
     * Total operationes exoneradas
     *
     * Formula = SUM(EXEMPTED_OPERATIONS_X_ITEM) - DESCUENTOS_GLOBALES + CARGOS_GLOBALES
     *
     * @return float
     */
    public function getTotalExemptedOperations()
    {
        $totalItems = $this->_items->getTotalExemptedOperations();
        return $this->applyAllowancesAndCharges($totalItems);
    }

    /**
     * Total operaciones inafectas
     * @return float
     */
    public function getTotalUnaffectedOperations()
    {
        $totalItems = $this->_items->getTotalUnaffectedOperations();
        return $this->applyAllowancesAndCharges($totalItems);
    }

    /**
     * Valor de venta
     *
     * Valor total de la factura sin considerar descuentos impuestos u otros tributos
     * @return float
     */
    public function getBillableValue()
    {
        return $this->_items->getTotalBillableValue();
    }

    /**
     * Total descuentos
     *
     * Formula: SUM(DESCUENTOS_X_ITEM) + DESCUENTOS_GLOBALES
     * @return float
     */
    public function getTotalAllowances()
    {
        $totalItems = $this->_items->getTotalAllowances();
        $totalBillableAmount = $this->getBillableAmount();
        $totalGlobal = Operations::getTotalAllowances($totalBillableAmount, $this->allowancesAndCharges);
        return $totalItems + $totalGlobal;
    }

    /**
     * Total cargos
     *
     * Formula: SUM(CARGOS_X_ITEM) + CARGOS_GLOBALES
     * @return float
     */
    public function getTotalCharges()
    {
        $totalItems = $this->_items->getTotalCharges();
        $totalBillableAmount = $this->getBillableAmount();
        $totalGlobal = Operations::getTotalCharges($totalBillableAmount, $this->allowancesAndCharges);
        return $totalItems + $totalGlobal;
    }

    /**
     * Total a pagar
     *
     * El importe que el usuario está obligado a pagar
     *
     * Formula = OPERACIONES_GRAVADAS + IGV + OPERACIONES_EXONERADAS + OPERACIONES_INAFECTAS
     *
     * @return float
     */
    public function getPayableAmount()
    {
        // Totals
        $totalTaxableOperations = $this->getTotalTaxableOperations();
        $totalIGV = $this->getIGV();
        $totalExemptedOperations = $this->getTotalExemptedOperations();
        return $totalTaxableOperations + $totalIGV + $totalExemptedOperations;
    }

    /**
     *
     * Total impuestos
     *
     * Fórmula: IGV + ISC + IVAP
     *
     * @return float
     */
    public function getTotalTaxes()
    {
        $IGV = $this->getIGV();
        $ISC = $this->getISC();
        $IVAP = $this->getIVAP();
        return $IGV + $ISC + $IVAP;
    }

    /**
     * IGV
     * @return float
     */
    public function getIGV()
    {
        $baseAmount = $this->getTaxableAmount();
        return Operations::calcIGV($baseAmount);
    }

    /**
     * ISC
     * @IMP
     * @return float
     */
    public function getISC()
    {
        return Operations::calcISC();
    }

    /**
     * IVAP
     * @IMP
     * @return float
     */
    public function getIVAP()
    {
        return Operations::calcIVAP();
    }

    /**
     *
     * @param float $amount
     * @return float
     */
    private function applyAllowancesAndCharges($amount)
    {
        return Operations::applyAllowancesAndCharges($amount, $this->allowancesAndCharges);
    }

    public function getDocumentName()
    {
        return Company::getRUC() . '-' . $this->documentType . '-' . $this->documentId;
    }
}
