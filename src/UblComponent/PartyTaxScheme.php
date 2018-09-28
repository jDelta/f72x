<?php

/**
 * FACTURA ELECTRÓNICA SUNAT
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\UblComponent;

use Sabre\Xml\Writer;
use Sabre\Xml\Element\Cdata;

class PartyTaxScheme extends BaseComponent {

    protected $RegistrationName;
    protected $CompanyID;
    protected $schemeID;

    /** @var RegistrationAddress */
    protected $RegistrationAddress;

    /** @var TaxScheme */
    protected $TaxScheme;

    function xmlSerialize(Writer $writer) {
        $writer->write([
            SchemaNS::CBC . 'RegistrationName'  => new Cdata($this->RegistrationName),
            [
                'name'          => SchemaNS::CBC . 'CompanyID',
                'value'         => $this->CompanyID,
                'attributes'    => [
                    'schemeID'          => $this->schemeID,
                    'schemeName'        => 'SUNAT:Identificador de Documento de Identidad',
                    'schemeAgencyName'  => 'PE:SUNAT',
                    'schemeURI'         => 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06'
                ]
            ]
        ]);
        if ($this->RegistrationAddress) {
            $writer->write([
                SchemaNS::CAC . 'RegistrationAddress' => $this->RegistrationAddress
            ]);
        }
        $writer->write([
            SchemaNS::CAC . 'TaxScheme'         => $this->TaxScheme
        ]);
    }

    public function getRegistrationName() {
        return $this->RegistrationName;
    }

    public function setRegistrationName($RegistrationName) {
        $this->RegistrationName = $RegistrationName;
        return $this;
    }

    public function getCompanyID() {
        return $this->CompanyID;
    }

    public function setCompanyID($CompanyID) {
        $this->CompanyID = $CompanyID;
        return $this;
    }
    public function getSchemeID() {
        return $this->schemeID;
    }

    public function setSchemeID($schemeID) {
        $this->schemeID = $schemeID;
        return $this;
    }

    public function getRegistrationAddress() {
        return $this->RegistrationAddress;
    }

    public function setRegistrationAddress(RegistrationAddress $RegistrationAddress) {
        $this->RegistrationAddress = $RegistrationAddress;
        return $this;
    }

    public function getTaxScheme() {
        return $this->TaxScheme;
    }

    public function setTaxScheme(TaxScheme $TaxScheme) {
        $this->TaxScheme = $TaxScheme;
        return $this;
    }

}
