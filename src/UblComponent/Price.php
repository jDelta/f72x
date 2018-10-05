<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\UblComponent;

use F72X\Sunat\Operations;
use Sabre\Xml\Writer;

class Price extends BaseComponent {
    
    const DECIMALS = 2;

    protected $currencyID;
    protected $PriceAmount;

    protected $validations = ['currencyID', 'PriceAmount'];


    function xmlSerialize(Writer $writer) {
        $this->validate();

        $writer->write([
            [
                'name'  => SchemaNS::CBC . 'PriceAmount',
                'value' => Operations::formatAmount($this->PriceAmount, self::DECIMALS),
                'attributes' => [
                    'currencyID' => $this->currencyID
                ]
            ],
        ]);

    }

    public function getCurrencyID() {
        return $this->currencyID;
    }

    public function setCurrencyID($currencyID) {
        $this->currencyID = $currencyID;
        return $this;
    }

    public function getPriceAmount() {
        return $this->PriceAmount;
    }

    public function setPriceAmount($PriceAmount) {
        $this->PriceAmount = $PriceAmount;
        return $this;
    }

}
