<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 *
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat\Document;

use F72X\Company;
use F72X\Sunat\DataMap;
use F72X\Sunat\InvoiceItems;
use F72X\Sunat\Catalogo;
use F72X\Sunat\SunatVars;
use F72X\Tools\UblHelper;
use F72X\UblComponent\OrderReference;
use F72X\UblComponent\Party;
use F72X\UblComponent\PartyIdentification;
use F72X\UblComponent\PartyTaxScheme;
use F72X\UblComponent\RegistrationAddress;
use F72X\UblComponent\PartyName;
use F72X\UblComponent\AccountingSupplierParty;
use F72X\UblComponent\AccountingCustomerParty;
use F72X\UblComponent\PartyLegalEntity;
use F72X\UblComponent\TaxTotal;
use F72X\UblComponent\TaxSubTotal;
use F72X\UblComponent\TaxCategory;
use F72X\UblComponent\TaxScheme;
use F72X\UblComponent\LegalMonetaryTotal;
use F72X\UblComponent\InvoiceLine;
use F72X\UblComponent\CreditNoteLine;
use F72X\UblComponent\DebitNoteLine;
use F72X\UblComponent\PricingReference;
use F72X\UblComponent\AlternativeConditionPrice;
use F72X\UblComponent\Amount;
use F72X\UblComponent\Item;
use F72X\UblComponent\SellersItemIdentification;
use F72X\UblComponent\CommodityClassification;
use F72X\UblComponent\PaymentTerms;
use F72X\UblComponent\Price;

trait BillMixin
{



    /** @var DataMap */
    private $dataMap;

    public function getDataMap()
    {
        return $this->dataMap;
    }

    /**
     *
     * @return InvoiceItems
     */
    public function getItems()
    {
        return $this->dataMap->getItems();
    }

    public function addPaymentTerms()
    {
        $invoice = $this->getDataMap();
        $currencyCode = $invoice->getCurrencyCode();
        $formOfPayment = $invoice->getFormOfPayment();
        $terms = [];
        // Contado
        if ($formOfPayment == 'Contado') {
            $terms[] = new PaymentTerms('FormaPago', 'Contado');
        }
        // Crédito
        elseif ($formOfPayment == 'Credito') {
            $paymentTerms = new PaymentTerms('FormaPago', 'Credito');
            $paymentTerms->setAmount(new Amount($invoice->getAmountToPayOnCredit(), $currencyCode));
            $terms[] = $paymentTerms;
            foreach ($invoice->getCrditInstallments() as $installement) {
                $paymentTerms = new PaymentTerms('FormaPago', $installement->getId());
                $paymentTerms->setAmount(new Amount($installement->getAmmount(), $currencyCode));
                $paymentTerms->setPaymentDueDate($installement->getPaymentDueDate());
                $terms[] = $paymentTerms;
            }
        }
        parent::setPaymentTerms($terms);
    }
    /**
     *
     * @param string $lineType InvoiceLine|CreditNoteLine|DebitNoteLine
     */
    private function addDocumentItems($lineType)
    {
        $ln = $this->dataMap->getTotalItems();
        // Loop
        for ($i = 0; $i < $ln; $i++) {
            $this->addDocumentItem($i, $lineType);
        }
    }

    private function addInvoiceOrderReference()
    {
        $orderNumer = $this->dataMap->getPurchaseOrder();
        if ($orderNumer) {
            // Xml Node
            $orderRef = new OrderReference();
            $orderRef->setID($orderNumer);
            // Añadir al documento
            parent::setOrderReference($orderRef);
        }
    }

    private function addDocumentTaxes()
    {
        $Invoice                   = $this->dataMap;
        $currencyID                = $Invoice->getCurrencyCode();              // Tipo de moneda
        $totalTaxableOperations    = $Invoice->getTotalTaxableOperations();    // Total operaciones gravadas
        $totalTaxes                = $Invoice->getTotalTaxes();                // Total operaciones gravadas
        $Igv                       = $Invoice->getIGV();                       // Total IGV
        $totalExemptedOperations   = $Invoice->getTotalExemptedOperations();   // Total operaciones exoneradas
        $totalUnaffectedOperations = $Invoice->getTotalUnaffectedOperations(); // Total operaciones inafectas
        $totalFreeOpertions        = $Invoice->getTotalFreeOperations();       // Total operaciones gratuitas

        // XML nodes
        $TaxTotal = new TaxTotal();

        // Operaciones gravadas
        if ($Igv) {
            UblHelper::addTaxSubtotal($TaxTotal, $currencyID, $Igv, $totalTaxableOperations, Catalogo::CAT5_IGV);
        }
        // Total operaciones exoneradas
        if ($totalExemptedOperations) {
            UblHelper::addTaxSubtotal($TaxTotal, $currencyID, 0, $totalExemptedOperations,   Catalogo::CAT5_EXO);
        }
        // Total operaciones inafectas
        if ($totalUnaffectedOperations) {
            UblHelper::addTaxSubtotal($TaxTotal, $currencyID, 0, $totalUnaffectedOperations, Catalogo::CAT5_INA);
        }
        // Total operaciones gratuitas
        if ($totalFreeOpertions) {
            UblHelper::addTaxSubtotal($TaxTotal, $currencyID, 0, $totalFreeOpertions,        Catalogo::CAT5_GRA);
        }

        // Total impuestos
        $TaxTotal
            ->setCurrencyID($currencyID)
            ->setTaxAmount($totalTaxes);
        // Anadir al documento
        parent::setTaxTotal($TaxTotal);
    }

    /**
     *
     * @param int $itemIndex Index del item
     * @param string $lineType InvoiceLine|CreditNoteLine|DebitNoteLine
     */

    private function addDocumentItem($itemIndex, $lineType)
    {
        $docLineClassName = "\F72X\UblComponent\\$lineType";
        // XML Nodes
        $DocumentLine     = new $docLineClassName();
        $PricingReference = new PricingReference();
        $TaxTotal         = new TaxTotal();
        $TaxSubTotal      = new TaxSubTotal();
        $TaxCategory      = new TaxCategory();
        $TaxCategory
            ->setElementAttributes('ID', [
                'schemeID'         => 'UN/ECE 5305',
                'schemeName'       => 'Tax Category Identifier',
                'schemeAgencyName' => 'United Nations Economic Commission for Europe'
            ])
            ->setElementAttributes('TaxExemptionReasonCode', [
                'listAgencyName'   => 'PE:SUNAT',
                'listName'         => 'SUNAT:Codigo de Tipo de Afectación del IGV',
                'listURI'          => 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07'
            ]);

        $TaxScheme = new TaxScheme();
        $TaxScheme
            ->setElementAttributes('ID', [
                'schemeID'         => 'UN/ECE 5153',
                'schemeName'       => 'Tax Scheme Identifier',
                'schemeAgencyName' => 'United Nations Economic Commission for Europe'
            ]);

        $AlternativeConditionPrice  = new AlternativeConditionPrice();
        $Item                       = new Item();
        $SellersItemIdentification  = new SellersItemIdentification();
        $CommodityClassification    = new CommodityClassification();
        $Price                      = new Price();
        // Detail Operation Matrix
        $Items = $this->dataMap->getItems();
        // Vars
        $productCode        = $Items->getProductCode($itemIndex);
        $sunatProductCode   = $Items->getUNPSC($itemIndex);
        $unitCode           = $Items->getUnitCode($itemIndex);
        $quantity           = $Items->getQunatity($itemIndex);
        $description        = $Items->getDescription($itemIndex);
        $currencyCode       = $Items->getCurrencyCode($itemIndex);
        $unitBillableValue  = $Items->getUnitBillableValue($itemIndex);
        $priceTypeCode      = $Items->getPriceTypeCode($itemIndex);
        $taxTypeCode        = $Items->getTaxTypeCode($itemIndex);
        $igvAffectationType = $Items->getIgvAffectationType($itemIndex);
        $taxCategoryPercent = $igvAffectationType === '10' ? SunatVars::IGV_PERCENT : '0.00';

        $itemValue          = $Items->getItemValue($itemIndex);
        $ac                 = $Items->getAllowancesAndCharges($itemIndex);
        $itemTaxableAmount  = $Items->getTaxableAmount($itemIndex);
        $itemTaxAmount      = $Items->getIgv($itemIndex);
        $unitPrice          = $Items->getUnitTaxedValue($itemIndex);

        // Catálogo 5 Ipuesto aplicable
        $cat5Item = Catalogo::getCatItem(5, $taxTypeCode);

        // Descuentos y cargos
        UblHelper::addAllowancesCharges($DocumentLine, $ac, $itemValue, $currencyCode);

        // Config Item
        $Item->setDescription($description); // Descripción
        // Código de producto
        if ($productCode) {
            $Item->setSellersItemIdentification($SellersItemIdentification->setID($productCode));
        }
        // Código de producto SUNAT
        if ($sunatProductCode) {
            $Item->setCommodityClassification($CommodityClassification->setItemClassificationCode($sunatProductCode));
        }
        $DocumentLine
            ->setCurrencyID($currencyCode)                    // Tipo de moneda
            ->setID($itemIndex + 1)                         // Número de orden
            ->setUnitCode($unitCode)                        // Codigo de unidad de medida
            ->setLineExtensionAmount($itemTaxableAmount)    // Valor de venta del ítem, sin impuestos
            ->setPricingReference($PricingReference
                ->setAlternativeConditionPrice($AlternativeConditionPrice
                    ->setCurrencyID($currencyCode)            // Tipo de moneda
                    ->setPriceAmount($unitPrice)            // Precio de venta unitario
                    ->setPriceTypeCode($priceTypeCode)))    // Price
            ->setTaxTotal($TaxTotal
                ->setCurrencyID($currencyCode)
                ->setTaxAmount($itemTaxAmount)
                ->addTaxSubTotal($TaxSubTotal
                    ->setCurrencyID($currencyCode)          // Tipo de moneda
                    ->setTaxableAmount($itemTaxableAmount)  // Valor de venta del item sin impuestos
                    ->setTaxAmount($itemTaxAmount)          // IGV
                    ->setTaxCategory($TaxCategory
                        ->setID($cat5Item['categoria'])                     // Codigo de categoria de immpuestos @CAT5
                        ->setPercent($taxCategoryPercent)                // Porcentaje de IGV (18.00)
                        ->setTaxExemptionReasonCode($igvAffectationType)    // Código de afectación del IGV
                        ->setTaxScheme($TaxScheme
                            ->setID($taxTypeCode)                       // Codigo de categoria de impuesto
                            ->setName($cat5Item['name'])
                            ->setTaxTypeCode($cat5Item['UN_ECE_5153'])))))
            ->setItem($Item)
            ->setPrice(
                $Price
                    ->setCurrencyID($currencyCode)    // Tipo de moneda
                    ->setPriceAmount($unitBillableValue)    // Precio unitario del item
            );
        // Set Quantity
        $this->setDocumentLineQuantity($DocumentLine, $lineType, $quantity);
        // Añade item
        $this->addDocumentLine($DocumentLine, $lineType);
    }

    /**
     *
     * @param InvoiceLine|CreditNoteLine|DebitNoteLine $DocumentLine
     * @param string $lineType InvoiceLine|CreditNoteLine|DebitNoteLine
     * @param int $quantity
     */
    private function addDocumentLine($DocumentLine, $lineType)
    {
        switch ($lineType) {
            case 'InvoiceLine':
                parent::addInvoiceLine($DocumentLine);
                break;
            case 'CreditNoteLine':
                parent::addCreditNoteLine($DocumentLine);
                break;
            case 'DebitNoteLine':
                parent::addDebitNoteLine($DocumentLine);
                break;
        }
    }
    /**
     *
     * @param InvoiceLine|CreditNoteLine|DebitNoteLine $DocumentLine
     * @param string $lineType InvoiceLine|CreditNoteLine|DebitNoteLine
     * @param int $quantity
     */
    private function setDocumentLineQuantity($DocumentLine, $lineType, $quantity)
    {
        switch ($lineType) {
            case 'InvoiceLine':
                $DocumentLine->setInvoicedQuantity($quantity);
                break;
            case 'CreditNoteLine':
                $DocumentLine->setCreditedQuantity($quantity);
                break;
            case 'DebitNoteLine':
                $DocumentLine->setDebitedQuantity($quantity);
                break;
        }
    }

    private function addInvoiceLegalMonetaryTotal()
    {
        $Invoice            = $this->dataMap;
        $currencyID         = $this->getDocumentCurrencyCode(); // Tipo de moneda
        $totalAllowances    = $Invoice->getTotalAllowances();   // Total descuentos
        $payableAmount      = $Invoice->getPayableAmount();     // Total a pagar
        $billableAmount     = $Invoice->getBillableValue();
        // LegalMonetaryTotal
        $LegalMonetaryTotal = new LegalMonetaryTotal();
        $LegalMonetaryTotal
            ->setCurrencyID($currencyID)
            ->setLineExtensionAmount($billableAmount)
            ->setTaxInclusiveAmount($payableAmount)
            ->setAllowanceTotalAmount($totalAllowances)
            ->setPayableAmount($payableAmount);

        parent::setLegalMonetaryTotal($LegalMonetaryTotal);
    }

    private function addInvoiceAccountingSupplierParty()
    {
        // Info
        $partyName  = Company::getBusinessName();
        $regName    = Company::getCompanyName();
        $docNumber  = Company::getRUC();
        $addressRegCode = Company::getRegAddressCode(); // Código de domicilio fiscal o anexo
        $docType    = Catalogo::IDENTIFICATION_DOC_RUC;

        // XML nodes
        $AccountingSupplierParty    = new AccountingSupplierParty();
        $Party                      = new Party();
        $PartyIdentification        = new PartyIdentification();
        $PartyTaxScheme             = new PartyTaxScheme();
        $RegistrationAddress        = new RegistrationAddress();
        $PartyIdentification
            ->setElementAttributes('ID', [
                'schemeAgencyName'  => 'PE:SUNAT',
                'schemeID'          => $docType,
                'schemeName'        => 'Documento de Identidad',
                'schemeURI'         => 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06'
            ]);
        $PartyName                  = new PartyName();
        $PartyLegalEntity           = new PartyLegalEntity();

        $AccountingSupplierParty
            ->setParty($Party
                ->setPartyIdentification($PartyIdentification
                    ->setID($docNumber))
                ->setPartyName($PartyName
                    ->setName($partyName))
                ->setPartyTaxScheme($PartyTaxScheme
                    ->setRegistrationAddress($RegistrationAddress
                        ->setAddressTypeCode($addressRegCode)))
                ->setPartyLegalEntity($PartyLegalEntity
                    ->setRegistrationName($regName)
                    ->setRegistrationAddress($RegistrationAddress
                        ->setAddressTypeCode($addressRegCode))));
        // Add to Document
        parent::setAccountingSupplierParty($AccountingSupplierParty);
    }

    private function addInvoiceAccountingCustomerParty()
    {
        $Invoice   = $this->dataMap;
        // Info
        $regName   = $Invoice->getCustomerRegName();
        $docNumber = $Invoice->getCustomerDocNumber();
        $docType   = $Invoice->getCustomerDocType();

        // XML nodes
        $AccountingCustomerParty    = new AccountingCustomerParty();
        $Party                      = new Party();
        $PartyIdentification        = new PartyIdentification();
        $PartyLegalEntity           = new PartyLegalEntity();
        $PartyIdentification
            ->setElementAttributes('ID', [
                'schemeAgencyName'  => 'PE:SUNAT',
                'schemeID'          => $docType,
                'schemeName'        => 'Documento de Identidad',
                'schemeURI'         => 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06'
            ]);

        $AccountingCustomerParty
            ->setParty($Party
                ->setPartyIdentification($PartyIdentification
                    ->setID($docNumber))
                ->setPartyLegalEntity($PartyLegalEntity
                    ->setRegistrationName($regName)));
        // Add to Document
        parent::setAccountingCustomerParty($AccountingCustomerParty);
    }

    /**
     *
     * @return string Nombre del comprobante de acuerdo con las especificaciones de la SUNAT
     */
    public function getDocumentName()
    {
        return $this->dataMap->getDocumentName();
    }
}
