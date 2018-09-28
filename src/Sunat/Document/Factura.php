<?php

/**
 * FACTURA ELECTRÓNICA SUNAT
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat\Document;

use F72X\Company;
use F72X\Tools\TemplateMgr;
use F72X\UblComponent\SchemaNS;
use Sabre\Xml\Writer;

class Factura extends SunatDocument {

    // Valores predeterminados para una factura
    public function __construct() {
        $this->InvoiceTypeCode = SunatDocument::DOC_FACTURA;
    }

    /**
     * The xmlSerialize method is called during xml writing.
     *
     * @param Writer $writer
     * @return void
     */
    public function xmlSerialize(Writer $writer) {
        $companyRUC  = Company::getRUC();
        $companyName = Company::getCompanyName();
        // SchemaNS::EXT . 'UBLExtensions'
        $UBLExtensions = TemplateMgr::getTpl('UBLExtensions.xml');
        $Signature     = TemplateMgr::getTpl('Signature.xml', [
                    'RUC'         => $companyRUC,
                    'companyName' => $companyName
        ]);
        $this->writeLineJump($writer);
        $writer->writeRaw($UBLExtensions);
        
        $writer->write([
            SchemaNS::CBC . 'UBLVersionID'      => self::UBL_VERSION_ID,
            SchemaNS::CBC . 'CustomizationID'   => self::CUSTUMIZATION_ID,
            [
                'name' => SchemaNS::CBC . 'ProfileID',
                'value' => $this->ProfileID,
                'attributes' => [
                    'schemeName' => 'SUNAT:Identificador de Tipo de Operación',
                    'schemeAgencyName' => 'PE:SUNAT',
                    'schemeURI' => 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo17'
                ]
            ],
            SchemaNS::CBC . 'ID' => $this->ID,
            SchemaNS::CBC . 'IssueDate' => $this->IssueDate->format('Y-m-d'),
            SchemaNS::CBC . 'IssueTime' => $this->IssueDate->format('H:i:s')
        ]);
        if ($this->DueDate) {
            $writer->write([
                SchemaNS::CBC . 'DueDate' => $this->DueDate->format('Y-m-d')
            ]);
        }
        $writer->write([
            [
                'name' => SchemaNS::CBC . 'InvoiceTypeCode',
                'value' => $this->InvoiceTypeCode,
                'attributes' => [
                    'listAgencyName' => 'PE:SUNAT',
                    'listID'         => $this->ProfileID,
                    'listName'       => 'Tipo de Documento',
                    'listSchemeURI'  => 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo51',
                    'listURI'        => 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01',
                    'name'           => 'Tipo de Operacion'
                ]
            ]
        ]);

        // Write notes
        foreach ($this->Notes as $InvoiceLine) {
            $writer->write($InvoiceLine);
        }

        $writer->write([
            [
                'name'  => SchemaNS::CBC . 'DocumentCurrencyCode',
                'value' => $this->DocumentCurrencyCode,
                'attributes' => [
                    'listID'            => 'ISO 4217 Alpha',
                    'listName'          => 'Currency',
                    'listAgencyName'    => 'United Nations Economic Commission for Europe'
                ]
            ],
            SchemaNS::CBC . 'LineCountNumeric'          => $this->LineCountNumeric
        ]);

        // Order Reference
        if($this->OrderReference){
            $writer->write([
                SchemaNS::CAC . 'OrderReference'            => $this->OrderReference
            ]);
        }

        // Despatch Document Reference
        if($this->DespatchDocumentReference){
            $writer->write([
                SchemaNS::CAC . 'DespatchDocumentReference' => $this->DespatchDocumentReference
            ]);
        }
        // cac:Signature
        $this->writeLineJump($writer);
        $writer->writeRaw($Signature);
        $writer->write([
            SchemaNS::CAC . 'AccountingSupplierParty'   => $this->AccountingSupplierParty,
            SchemaNS::CAC . 'AccountingCustomerParty'   => $this->AccountingCustomerParty
        ]);
        // Cargos y descuentos
        foreach ($this->AllowanceCharges as $AllowanceCharge) {
            $writer->write([
                SchemaNS::CAC . 'AllowanceCharge' => $AllowanceCharge
            ]);
        }
        $writer->write([
            SchemaNS::CAC . 'TaxTotal' => $this->TaxTotal
        ]);
        $writer->write([
            SchemaNS::CAC . 'LegalMonetaryTotal' => $this->LegalMonetaryTotal
        ]);

        // Detalle
        foreach ($this->InvoiceLines as $InvoiceLine) {
            $writer->write([
                SchemaNS::CAC . 'InvoiceLine' => $InvoiceLine
            ]);
        }
    }

}
