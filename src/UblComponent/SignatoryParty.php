<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\UblComponent;

use Sabre\Xml\Writer;

class SignatoryParty extends BaseComponent {

    /** @var PartyIdentification */
    protected $PartyIdentification;

    /** @var PartyName */
    protected $PartyName;

    function xmlSerialize(Writer $writer) {
        $me = $this;
        $writer->write([
            SchemaNS::CAC . 'PartyIdentification'  => $me->PartyIdentification,
            SchemaNS::CAC . 'PartyName'         => $me->PartyName
        ]);
    }

    public function getPartyIdentification() {
        return $this->PartyIdentification;
    }

    public function setPartyIdentification(PartyIdentification $PartyIdentification) {
        $this->PartyIdentification = $PartyIdentification;
        return $this;
    }

    public function getPartyName() {
        return $this->PartyName;
    }

    public function setPartyName(PartyName $PartyName) {
        $this->PartyName = $PartyName;
        return $this;
    }

}
