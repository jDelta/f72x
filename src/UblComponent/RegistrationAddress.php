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

class RegistrationAddress extends BaseComponent {

    protected $AddressTypeCode;

    function xmlSerialize(Writer $writer) {
        $writer->write([
            SchemaNS::CBC . 'AddressTypeCode' => $this->AddressTypeCode
        ]);
    }

    public function getAddressTypeCode() {
        return $this->AddressTypeCode;
    }

    public function setAddressTypeCode($AddressTypeCode) {
        $this->AddressTypeCode = $AddressTypeCode;
        return $this;
    }

}
