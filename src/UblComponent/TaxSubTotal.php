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

class TaxSubTotal extends BaseComponent
{

    const DECIMALS = 2;

    protected $currencyID;
    protected $TaxableAmount;
    protected $TaxAmount;

    /** @var TaxCategory */
    protected $TaxCategory;
    protected $validations = ['currencyID', 'TaxableAmount', 'TaxAmount', 'TaxCategory'];

    function xmlSerialize(Writer $writer): void
    {
        $this->validate();

        $writer->write([
            [
                'name' => SchemaNS::CBC . 'TaxableAmount',
                'value' => Operations::formatAmount($this->TaxableAmount, self::DECIMALS),
                'attributes' => [
                    'currencyID' => $this->currencyID
                ]
            ],
            [
                'name' => SchemaNS::CBC . 'TaxAmount',
                'value' => Operations::formatAmount($this->TaxAmount, self::DECIMALS),
                'attributes' => [
                    'currencyID' => $this->currencyID
                ]
            ],
            SchemaNS::CAC . 'TaxCategory' => $this->TaxCategory
        ]);
    }

    public function getTaxableAmount()
    {
        return $this->TaxableAmount;
    }

    public function setTaxableAmount($TaxableAmount)
    {
        $this->TaxableAmount = $TaxableAmount;
        return $this;
    }

    public function getTaxAmount()
    {
        return $this->TaxAmount;
    }

    public function setTaxAmount($TaxAmount)
    {
        $this->TaxAmount = $TaxAmount;
        return $this;
    }

    public function getCurrencyID()
    {
        return $this->currencyID;
    }

    public function setCurrencyID($currencyID)
    {
        $this->currencyID = $currencyID;
        return $this;
    }

    public function getTaxCategory()
    {
        return $this->TaxCategory;
    }

    public function setTaxCategory($TaxCategory)
    {
        $this->TaxCategory = $TaxCategory;
        return $this;
    }

}
