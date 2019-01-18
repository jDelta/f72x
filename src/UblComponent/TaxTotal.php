<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\UblComponent;

use F72X\Sunat\Operations;
use Sabre\Xml\Writer;

class TaxTotal extends BaseComponent {
    
    const DECIMALS = 2;

    protected $currencyID;
    protected $TaxAmount;

    /** @var TaxSubTotal[] */
    protected $TaxSubTotals = [];
    protected $validations = ['TaxAmount', 'currencyID', 'TaxSubTotals'];

    

    /**
     * The xmlSerialize method is called during xml writing.
     * @param Writer $writer
     * @return void
     */
    function xmlSerialize(Writer $writer) {
        $this->validate();

        $writer->write([
            [
                'name'  => SchemaNS::CBC . 'TaxAmount',
                'value' => Operations::formatAmount($this->TaxAmount, self::DECIMALS),
                'attributes' => [
                    'currencyID' => $this->currencyID
                ]
            ],
        ]);

        foreach ($this->TaxSubTotals as $taxSubTotal) {
            $writer->write([SchemaNS::CAC . 'TaxSubtotal' => $taxSubTotal]);
        }
    }

    public function getCurrencyID() {
        return $this->currencyID;
    }

    public function setCurrencyID($currencyID) {
        $this->currencyID = $currencyID;
        return $this;
    }

    public function getTaxAmount() {
        return $this->TaxAmount;
    }

    public function setTaxAmount($TaxAmount) {
        $this->TaxAmount = $TaxAmount;
        return $this;
    }

    public function getTaxSubTotals() {
        return $this->TaxSubTotals;
    }
    public function setTaxSubTotals($TaxSubTotals) {
        $this->TaxSubTotals = $TaxSubTotals;
        return $this;
    }

    public function addTaxSubTotal(TaxSubTotal $TaxSubTotal) {
        $this->TaxSubTotals[] = $TaxSubTotal;
        return $this;
    }

}
