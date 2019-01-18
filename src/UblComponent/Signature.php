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
use Sabre\Xml\XmlSerializable;

class Signature extends BaseComponent {

    protected $ID;

    /** @var SignatoryParty */
    protected $SignatoryParty;

    /** @var DigitalSignatureAttachment */
    protected $DigitalSignatureAttachment;

    function xmlSerialize(Writer $writer) {
        $me = $this;
        $writer->write([
            SchemaNS::CBC . 'ID'                            => $me->ID,
            SchemaNS::CAC . 'SignatoryParty'                => $me->SignatoryParty,
            SchemaNS::CAC . 'DigitalSignatureAttachment'    => $me->DigitalSignatureAttachment
        ]);
    }
    public function getID() {
        return $this->ID;
    }

    public function setID($ID) {
        $this->ID = $ID;
        return $this;
    }

    public function getSignatoryParty() {
        return $this->SignatoryParty;
    }

    public function setSignatoryParty(SignatoryParty $SignatoryParty) {
        $this->SignatoryParty = $SignatoryParty;
        return $this;
    }
    public function getDigitalSignatureAttachment() {
        return $this->DigitalSignatureAttachment;
    }

    public function setDigitalSignatureAttachment(DigitalSignatureAttachment $DigitalSignatureAttachment) {
        $this->DigitalSignatureAttachment = $DigitalSignatureAttachment;
        return $this;
    }

}
