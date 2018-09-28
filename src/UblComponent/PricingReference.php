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

class PricingReference extends BaseComponent {

    /** @var AlternativeConditionPrice */
    protected $AlternativeConditionPrice;
    protected $validations = ['AlternativeConditionPrice'];

    function xmlSerialize(Writer $writer) {
        $this->validate();
        $writer->write([
            SchemaNS::CAC . 'AlternativeConditionPrice' => $this->AlternativeConditionPrice
        ]);
    }

    public function getAlternativeConditionPrice() {
        return $this->AlternativeConditionPrice;
    }

    public function setAlternativeConditionPrice(AlternativeConditionPrice $AlternativeConditionPrice) {
        $this->AlternativeConditionPrice = $AlternativeConditionPrice;
        return $this;
    }

}
