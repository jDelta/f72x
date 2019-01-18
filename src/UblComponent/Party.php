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

class Party extends BaseComponent {

    /** @var PartyIdentification */
    protected $PartyIdentification;

    /** @var PartyName */
    protected $PartyName;

    /** @var PartyTaxScheme */
    protected $PartyTaxScheme;

    /** @var PartyLegalEntity */
    protected $PartyLegalEntity;

    function xmlSerialize(Writer $writer) {
        if ($this->PartyIdentification) {
            $writer->write([
                SchemaNS::CAC . 'PartyIdentification' => $this->PartyIdentification
            ]);
        }
        if ($this->PartyName) {
            $writer->write([
                SchemaNS::CAC . 'PartyName' => $this->PartyName
            ]);
        }
        if ($this->PartyTaxScheme) {
            $writer->write([
                SchemaNS::CAC . 'PartyTaxScheme' => $this->PartyTaxScheme
            ]);
        }
        if ($this->PartyLegalEntity) {
            $writer->write([
                SchemaNS::CAC . 'PartyLegalEntity' => $this->PartyLegalEntity
            ]);
        }
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

    public function getPartyTaxScheme() {
        return $this->PartyTaxScheme;
    }

    public function setPartyTaxScheme($PartyTaxScheme) {
        $this->PartyTaxScheme = $PartyTaxScheme;
        return $this;
    }

    public function getPartyLegalEntity() {
        return $this->PartyLegalEntity;
    }

    public function setPartyLegalEntity($PartyLegalEntity) {
        $this->PartyLegalEntity = $PartyLegalEntity;
        return $this;
    }

}
