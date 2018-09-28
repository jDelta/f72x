<?php

/**
 * FACTURA ELECTRÓNICA SUNAT
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat;

use InvalidArgumentException;
use DateTime;

use F72X\Tools\XmlService;
use F72X\Tools\XmlDSig;
use F72X\Tools\FileService;
use F72X\Company;
use F72X\Sunat\Document\SunatDocument;
use F72X\Sunat\Document\Factura;
use F72X\Sunat\SunatVars;

use F72X\UblComponent\OrderReference;
use F72X\UblComponent\Signature;
use F72X\UblComponent\SignatoryParty;
use F72X\UblComponent\Party;
use F72X\UblComponent\PartyIdentification;
use F72X\UblComponent\PartyName;
use F72X\UblComponent\DigitalSignatureAttachment;
use F72X\UblComponent\ExternalReference;
use F72X\UblComponent\AccountingSupplierParty;
use F72X\UblComponent\AccountingCustomerParty;
use F72X\UblComponent\PartyLegalEntity;
use F72X\UblComponent\TaxTotal;
use F72X\UblComponent\TaxSubTotal;
use F72X\UblComponent\TaxCategory;
use F72X\UblComponent\TaxScheme;
use F72X\UblComponent\LegalMonetaryTotal;
use F72X\UblComponent\InvoiceLine;
use F72X\UblComponent\AllowanceCharge;
use F72X\UblComponent\PricingReference;
use F72X\UblComponent\AlternativeConditionPrice;
use F72X\UblComponent\Item;
use F72X\UblComponent\SellersItemIdentification;
use F72X\UblComponent\CommodityClassification;
use F72X\UblComponent\Price;

class DocumentGenerator {

    public static function generateFactura($data, $currencyID = 'PEN') {

        if (!is_array($data)) {
            throw new InvalidArgumentException('$data, Se esperá un array!');
        }

        // Documento XML para la factura
        $Document = new Factura();

        // Tipo de operación
        $Document->setProfileID($data['operationTypeCode']);

        // ID Serie + Numero
        $ID = 'F' . str_pad($data['voucherSeries'], 3, '0', STR_PAD_LEFT) . '-' . str_pad($data['voucherNumber'], 8, '0', STR_PAD_LEFT);
        $Document->setID($ID);

        // Fecha de emisión
        $IssueDate = isset($data['date']) ? $data['date'] : new DateTime();
        $Document->setIssueDate($IssueDate);

        // Currency code
        $Document->setDocumentCurrencyCode($currencyID);

        // LineCountNumeric: Total items
        $Document->setLineCountNumeric(count($data['items']));

        // Reference order
        $orderRef = new OrderReference();
            $orderRef->setID($data['purchaseOrder']);
        $Document->setOrderReference($orderRef);

        // AccountingSupplierParty: Company info
        self::addAccountingSupplierParty($Document);

        // 11. AccountingCustomerParty: Customer info
        self::addAccountingCustomerParty($Document, $data);

        // Detalle
        self::addInvoiceLines($Document, $data['items']);
        
        // Detail opertions matrix
        $DOM = $Document->getDetailMatrix();

        // Descuentos globales
        if (isset($data['allowances'])) {
            foreach ($data['allowances'] as $discount) {
                $baseAmount = $DOM->getTotalTaxableAmount();
                $k = $discount['multiplierFactor'];
                $amount = $baseAmount * $k;
                self::addAllowanceCharge($Document, $currencyID, 'false', $discount['reasonCode'], $discount['multiplierFactor'], $amount, $baseAmount);
            }
        }

        // Impuestos
        self::addTaxes($Document);

        // Totales
        self::addLegalMonetaryTotal($Document);
        
        // Save Document
        self::saveInvoice($Document);
        // Sign Document
        self::singInvoice($Document);
        // Compress Document
        self::zipInvoice($Document);
    }
    private static function singInvoice(SunatDocument $Document){
        $xmlFile = $Document->getFileName();
        XmlDSig::sign($xmlFile);
    }
    private static function zipInvoice(SunatDocument $Document){
        $xmlFile = $Document->getFileName();
        FileService::doZip($xmlFile);
    }
    private static function addLegalMonetaryTotal(SunatDocument $Invoice) {

        $DOM                = $Invoice->getDetailMatrix();
        $currencyID         = $Invoice->getDocumentCurrencyCode();  // Tipo de moneda
        $itemsAllowance     = $DOM->getTotalAllowances();           // Total descuentos por item
        $taxableOperations  = $DOM->getTotalTaxableOperations();    // Total operaciones gravadas
        $exemptedOperations = $DOM->getTotalExcemptedOperations();  // Total operaciones exoneradas

        // Total descuentos globales
        $gloabalAllowance = 0;
        $AllowanceCharges = $Invoice->getAllowanceCharges();
        foreach ($AllowanceCharges as $AllowanceCharge) {
            if ($AllowanceCharge->getChargeIndicator() === 'false') {
                $gloabalAllowance += $AllowanceCharge->getAmount();
            }
        }
        // Descuentos totales = Total descuentos globales + Total descuentos por item
        $totalAllowance = $gloabalAllowance + $itemsAllowance;
        
        // Total a pagar = (Operaciones gravadas - descuentos + cargos)*(1+IGV) + (Operaciones exóneradas - descuentos + cargos)
        $payableAmount = self::applyAllowancesAndCharges($Invoice, $taxableOperations) * (1 + SunatVars::IGV) + self::applyAllowancesAndCharges($Invoice, $exemptedOperations);
        // LegalMonetaryTotal
        $LegalMonetaryTotal = new LegalMonetaryTotal();
        $LegalMonetaryTotal
                ->setCurrencyID($currencyID)
                ->setLineExtensionAmount($DOM->getTotalPayableValue()) // Valor de la venta
                ->setTaxInclusiveAmount($payableAmount)         // Valor de la venta incluido impuestos
                ->setAllowanceTotalAmount($totalAllowance)
                ->setPayableAmount($payableAmount);

        $Invoice->setLegalMonetaryTotal($LegalMonetaryTotal);
    }

    /**
     * 
     * @param SunatDocument|InvoiceLine $source
     * @param float $baseAmount
     * @return float
     */
    private static function applyAllowancesAndCharges($source, $baseAmount) {
        $AllowanceCharges = $source->getAllowanceCharges();
        $allowance = 0;
        $charge = 0;
        foreach ($AllowanceCharges as $AllowanceCharge) {
            $k = $AllowanceCharge->getMultiplierFactorNumeric();
            if ($AllowanceCharge->getChargeIndicator() === 'false') {
                $allowance += $baseAmount * $k;
            } else {
                $charge += $baseAmount * $k;
            }
        }
        return $baseAmount - $allowance + $charge;
    }

    private static function addInvoiceLines(SunatDocument $Invoice, $items) {
        $currencyID = $Invoice->getDocumentCurrencyCode();  // Tipo de moneda
        $DetailMatrix = new DetailMatrix();                 // Matriz de calculos
        $DetailMatrix->populate($items, $currencyID);
        $Invoice->setDetailMatrix($DetailMatrix);
        $ln = count($items);
        // Loop
        for ($i = 0; $i < $ln; $i++) {
            self::addInvoiceLine($Invoice, $i);
        }
    }

    /**
     * 
     * @param SunatDocument $Invoice
     * @param int $itemIndex Index del item
     */
    private static function addInvoiceLine(SunatDocument $Invoice, $itemIndex) {

        // XML Nodes
        $InvoiceLine      = new InvoiceLine();
        $PricingReference = new PricingReference();
        $TaxTotal         = new TaxTotal();
        $TaxSubTotal      = new TaxSubTotal();
        $TaxCategory      = new TaxCategory();
        $TaxCategory
                ->setElementAttributes('ID', [
                    'schemeID' => 'UN/ECE 5305',
                    'schemeName' => 'Tax Category Identifier',
                    'schemeAgencyName' => 'United Nations Economic Commission for Europe'])
                ->setElementAttributes('TaxExemptionReasonCode', [
                    'listAgencyName' => 'PE:SUNAT',
                    'listName' => 'SUNAT:Codigo de Tipo de Afectación del IGV',
                    'listURI' => 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07']);

        $TaxScheme = new TaxScheme();
        $TaxScheme
                ->setElementAttributes('ID', [
                    'schemeID' => 'UN/ECE 5153',
                    'schemeName' => 'Tax Scheme Identifier',
                    'schemeAgencyName' => 'United Nations Economic Commission for Europe']);

        $AlternativeConditionPrice  = new AlternativeConditionPrice();
        $Item                       = new Item();
        $SellersItemIdentification  = new SellersItemIdentification();
        $CommodityClassification    = new CommodityClassification();
        $Price                      = new Price();
        // Detail Operation Matrix
        $DOM = $Invoice->getDetailMatrix();
        // Vars
        $productCode        = $DOM->getProductCode($itemIndex);
        $sunatProductCode   = $DOM->getUNPSC($itemIndex);
        $unitCode           = $DOM->getUnitCode($itemIndex);
        $quantity           = $DOM->getQunatity($itemIndex);
        $description        = $DOM->getDescription($itemIndex);
        $currencyID         = $DOM->getCurrencyCode($itemIndex);
        $unitBillableValue  = $DOM->getUnitBillableValue($itemIndex);
        $priceTypeCode      = $DOM->getPriceTypeCode($itemIndex);
        $taxTypeCode        = $DOM->getTaxTypeCode($itemIndex);
        $igvAffectationCode = $DOM->getIgvAffectationCode($itemIndex);
        
        $itemValue          = $DOM->getItemValue($itemIndex);
        $allowances         = $DOM->getAllowances($itemIndex);
        $charges            = $DOM->getCharges($itemIndex);
        $itemTaxableAmount  = $DOM->getTaxableAmount($itemIndex);
        $itemTaxAmount      = $DOM->getIgv($itemIndex);
        $unitPrice          = $DOM->getUnitPayableAmount($itemIndex);

        // Catálogo 5 Ipuesto aplicable
        $cat5Item = Catalogo::getCatItem(5, $taxTypeCode);

        // Descuentos/Cargos
        // Descuentos
        foreach ($allowances as $item) {
            $multFactor = $item['multiplierFactor'];
            $amount = $itemValue * $multFactor;
            self::addAllowanceCharge($InvoiceLine, $currencyID, 'false', $item['reasonCode'], $multFactor, $amount, $itemValue);
        }
        // Cargos
        foreach ($charges as $item) {
            $multFactor = $item['multiplierFactor'];
            $amount = $itemValue * $multFactor;
            self::addAllowanceCharge($InvoiceLine, $currencyID, 'true', $item['reasonCode'], $multFactor, $amount, $itemValue);
        }

        $InvoiceLine
                ->setCurrencyID($currencyID)                    // Tipo de moneda
                ->setID($itemIndex + 1)                          // Número de orden
                ->setUnitCode($unitCode)                        // Codigo de unidad de medida
                ->setInvoicedQuantity($quantity)                // Cantidad
                ->setLineExtensionAmount($itemTaxableAmount)    // Valor de venta del ítem, sin impuestos
                ->setPricingReference($PricingReference
                        ->setAlternativeConditionPrice($AlternativeConditionPrice
                                ->setCurrencyID($currencyID)            // Tipo de moneda
                                ->setPriceAmount($unitPrice)            // Precio de venta unitario
                                ->setPriceTypeCode($priceTypeCode)))    // Price
                ->setTaxTotal($TaxTotal
                        ->setCurrencyID($currencyID)
                        ->setTaxAmount($itemTaxAmount)
                        ->addTaxSubTotal($TaxSubTotal
                                ->setCurrencyID($currencyID)            // Tipo de moneda
                                ->setTaxableAmount($itemTaxableAmount)  // Valor de venta del item sin impuestos
                                ->setTaxAmount($itemTaxAmount)          // IGV
                                ->setTaxCategory($TaxCategory
                                        ->setID($cat5Item['categoria'])                     // Codigo de categoria de immpuestos @CAT5
                                        ->setPercent(SunatVars::IGV_PERCENT)                // Porcentaje de IGV (18.00)
                                        ->setTaxExemptionReasonCode($igvAffectationCode)    // Código de afectación del IGV
                                        ->setTaxScheme($TaxScheme
                                                ->setID($taxTypeCode)                       // Codigo de categoria de impuesto
                                                ->setName($cat5Item['name'])
                                                ->setTaxTypeCode($cat5Item['UN_ECE_5153'])))))
                ->setItem($Item
                        ->setDescription($description)                              // Descripción
                        ->setSellersItemIdentification($SellersItemIdentification
                                ->setID($productCode))                              // Código de producto
                        ->setCommodityClassification($CommodityClassification
                                ->setItemClassificationCode($sunatProductCode)))    // Código de producto SUNAT
                ->setPrice($Price
                        ->setCurrencyID($currencyID)    // Tipo de moneda
                        ->setPriceAmount($unitBillableValue)    // Precio unitario del item
        );
        // Añade item
        $Invoice->addInvoiceLine($InvoiceLine);
    }

    /**
     * 
     * @param SunatDocument|InvoiceLine $target
     * @param string $currencyID
     * @param string $ChargeIndicator
     * @param string $AllowanceChargeReasonCode
     * @param float $Amount
     * @param float $BaseAmount
     */
    private static function addAllowanceCharge($target, $currencyID, $ChargeIndicator, $AllowanceChargeReasonCode, $MultiplierFactorNumeric, $Amount, $BaseAmount) {
        $AllowanceCharge = new AllowanceCharge();
        $AllowanceCharge
                ->setCurrencyID($currencyID)
                ->setChargeIndicator($ChargeIndicator)
                ->setAllowanceChargeReasonCode($AllowanceChargeReasonCode)
                ->setMultiplierFactorNumeric($MultiplierFactorNumeric)
                ->setAmount($Amount)
                ->setBaseAmount($BaseAmount);
        // Add AllowanceCharge
        $target
                ->addAllowanceCharge($AllowanceCharge);
    }

    private static function addTaxes(SunatDocument $Invoice) {
        $DOM                  = $Invoice->getDetailMatrix();
        $currencyID           = $Invoice->getDocumentCurrencyCode();  // Tipo de moneda
        $taxableOperations    = $DOM->getTotalTaxableOperations();    // Total operaciones gravadas
        $exemptedOperations   = $DOM->getTotalExcemptedOperations();  // Total operaciones exoneradas
        $unaffectedOperations = $DOM->getTotalUnaffectedOperations(); // Total operaciones inafectas

        // XML nodes
        $TaxTotal = new TaxTotal();


        // Operaciones gravadas - aplicando cargos y descuetos
        $igvBaseAmount = self::applyAllowancesAndCharges($Invoice, $taxableOperations);
        $taxAmount = $igvBaseAmount * SunatVars::IGV;
        self::addTaxSubtotal($TaxTotal, $currencyID, $taxAmount, $igvBaseAmount,    DetailMatrix::TAX_IGV);
        
        // Total operaciones exoneradas - aplicando cargos y descuetos
        $exoBaseAmount = self::applyAllowancesAndCharges($Invoice, $exemptedOperations);
        self::addTaxSubtotal($TaxTotal, $currencyID, 0, $exoBaseAmount,   DetailMatrix::TAX_EXO);

        // Total operaciones inafectas
        self::addTaxSubtotal($TaxTotal, $currencyID, 0, $unaffectedOperations, DetailMatrix::TAX_INA);
        
        // Total impuestos
        $TaxTotal
                ->setCurrencyID($currencyID)
                ->setTaxAmount($taxAmount);
        // Anadir al documento
        $Invoice->setTaxTotal($TaxTotal);
    }

    private static function addTaxSubtotal(TaxTotal $TaxTotal, $currencyID, $taxAmount, $taxableAmount, $taxTypeCode) {
        // XML nodes
        $TaxSubTotal = new TaxSubTotal();
        $TaxCategory = new TaxCategory();
        $TaxCategory->setElementAttributes('ID', [
            'schemeID'          => 'UN/ECE 5305',
            'schemeName'        => 'Tax Category Identifier',
            'schemeAgencyName'  => 'United Nations Economic Commission for Europe'
        ]);
        $TaxScheme = new TaxScheme();
        $TaxScheme->setElementAttributes('ID', [
            'schemeID'          => 'UN/ECE 5153',
            'schemeAgencyID'    => '6']);

        $cat5Item = Catalogo::getCatItem(5, $taxTypeCode);
        
        $TaxSubTotal
                ->setCurrencyID($currencyID)
                ->setTaxAmount($taxAmount)
                ->setTaxableAmount($taxableAmount)
                ->setTaxCategory($TaxCategory
                        ->setID($cat5Item['categoria'])
                        ->setTaxScheme($TaxScheme
                                ->setID($taxTypeCode)
                                ->setName($cat5Item['name'])
                                ->setTaxTypeCode($cat5Item['UN_ECE_5153'])));
        $TaxTotal->addTaxSubTotal($TaxSubTotal);
    }

    public static function addAccountingSupplierParty(SunatDocument $Document) {
        // Info
        $partyName  = Company::getBusinessName();
        $regName    = Company::getCompanyName();
        $docNumber  = Company::getRUC();
        $docType    = Catalogo::IDENTIFICATION_DOC_RUC;
        
        // XML nodes
        $AccountingSupplierParty    = new AccountingSupplierParty();
        $Party                      = new Party();
        $PartyIdentification        = new PartyIdentification();
        $PartyIdentification
                ->setElementAttributes('ID', [
                    'schemeAgencyName'  => 'PE:SUNAT',
                    'schemeID'          => $docType,
                    'schemeName'        => 'Documento de Identidad',
                    'schemeURI'         => 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06']);
        $PartyName                  = new PartyName();
        $PartyLegalEntity           = new PartyLegalEntity();
        
        $AccountingSupplierParty
                ->setParty($Party
                        ->setPartyIdentification($PartyIdentification
                                ->setID($docNumber))
                        ->setPartyName($PartyName
                                ->setName($partyName))
                        ->setPartyLegalEntity($PartyLegalEntity
                                ->setRegistrationName($regName)));
        // Add to Document
        $Document->setAccountingSupplierParty($AccountingSupplierParty);
    }

    public static function addAccountingCustomerParty(SunatDocument $Document, $data) {
        // Info
        $regName     = $data['customerName'];
        $docNumber   = $data['customerDocNumber'];
        $docType     = $data['customerDocType'];
//        $taxSchemeID = '-';
        
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
                    'schemeURI'         => 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06']);
        
        $AccountingCustomerParty
                ->setParty($Party
                        ->setPartyIdentification($PartyIdentification
                                ->setID($docNumber))
                        ->setPartyLegalEntity($PartyLegalEntity
                                ->setRegistrationName($regName)));
        // Add to Document
        $Document->setAccountingCustomerParty($AccountingCustomerParty);
    }

    public static function saveInvoice(SunatDocument $invoice) {
        $repository = Company::getRepositoryPath();
        $xmlService = new XmlService('1.0', 'ISO-8859-1');

        $xmlService->namespaceMap = [
            "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"                        => '',
            "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"      => 'cac',
            "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"          => 'cbc',
            "urn:un:unece:uncefact:documentation:2"                                         => 'ccts',
            "http://www.w3.org/2000/09/xmldsig#"                                            => 'ds',
            "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"      => 'ext',
            "urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2"             => 'qdt',
            "urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2"   => 'udt',
            "http://www.w3.org/2001/XMLSchema-instance"                                     => 'xsi'
        ];
        $fileName = $invoice->getFileName();
        file_put_contents("$repository/xml/$fileName", $xmlService->write('Invoice', $invoice));
    }

}
