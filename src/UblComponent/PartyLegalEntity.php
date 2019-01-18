<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\UblComponent;

use Sabre\Xml\Writer;
use Sabre\Xml\Element\Cdata;

class PartyLegalEntity extends BaseComponent {

    protected $RegistrationName;

    /** @var RegistrationAddress */
    protected $RegistrationAddress;
    
    function xmlSerialize(Writer $writer) {
        $writer->write([
            SchemaNS::CBC . 'RegistrationName' => new Cdata($this->RegistrationName)
        ]);
        if ($this->RegistrationAddress) {
            $writer->write([
                SchemaNS::CAC . 'RegistrationAddress' => $this->RegistrationAddress
            ]);
        }
    }

    public function getRegistrationName() {
        return $this->RegistrationName;
    }

    public function setRegistrationName($RegistrationName) {
        $this->RegistrationName = $RegistrationName;
        return $this;
    }

    public function getRegistrationAddress() {
        return $this->RegistrationAddress;
    }

    public function setRegistrationAddress(RegistrationAddress $RegistrationAddress) {
        $this->RegistrationAddress = $RegistrationAddress;
        return $this;
    }

}
