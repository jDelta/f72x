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

class Amount extends BaseComponent {

    /** @var string */
    protected $CurrencyID;

    /** @var decimal */
    protected $Value;

    public function __construct($Value, $CurrencyID) {
        $this->Value = $Value;
        $this->CurrencyID = $CurrencyID;
    }

    public function xmlSerialize(Writer $writer) {
        $writer->write([
            'name'  => SchemaNS::CBC . 'Amount',
            'value' => $this->Value,
            'attributes' => [
                'currencyID'  => $this->CurrencyID
            ]
        ]);
    }

    public function getCurrencyID() {
        return $this->CurrencyID;
    }

    public function getValue() {
        return $this->Value;
    }

}
