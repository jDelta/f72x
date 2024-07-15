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

class CreditNoteLine extends BaseComponent
{

    const DECIMALS = 2;

    protected $ID;
    protected $CreditedQuantity;
    protected $unitCode;
    protected $LineExtensionAmount;
    protected $currencyID;

    /** @var PricingReference */
    protected $PricingReference;

    /** @var TaxTotal */
    protected $TaxTotal;

    /** @var Item */
    protected $Item;

    /** @var Price */
    protected $Price;

    protected $validations = [
        'currencyID',
        'ID',
        'CreditedQuantity',
        'unitCode',
        'LineExtensionAmount',
        'PricingReference',
        'TaxTotal',
        'Item',
        'Price'
    ];

    function xmlSerialize(Writer $writer): void
    {
        $this->validate();
        $writer->write([
            SchemaNS::CBC . 'ID' => $this->ID,
            [
                'name' => SchemaNS::CBC . 'CreditedQuantity',
                'value' => $this->CreditedQuantity,
                'attributes' => [
                    'unitCode' => $this->unitCode,
                    'unitCodeListID' => 'UN/ECE rec 20',
                    'unitCodeListAgencyName' => 'United Nations Economic Commission for Europe'
                ]
            ],
            [
                'name' => SchemaNS::CBC . 'LineExtensionAmount',
                'value' => Operations::formatAmount($this->LineExtensionAmount, self::DECIMALS),
                'attributes' => [
                    'currencyID' => $this->currencyID
                ]
            ],
            SchemaNS::CAC . 'PricingReference' => $this->PricingReference
        ]);
        $writer->write([
            SchemaNS::CAC . 'TaxTotal' => $this->TaxTotal,
            SchemaNS::CAC . 'Item' => $this->Item,
            SchemaNS::CAC . 'Price' => $this->Price
        ]);

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

    public function getID()
    {
        return $this->ID;
    }

    public function setID($ID)
    {
        $this->ID = $ID;
        return $this;
    }
    public function getUnitCode()
    {
        return $this->unitCode;
    }

    public function setUnitCode($unitCode)
    {
        $this->unitCode = $unitCode;
        return $this;
    }

    public function getCreditedQuantity()
    {
        return $this->CreditedQuantity;
    }

    public function setCreditedQuantity($CreditedQuantity)
    {
        $this->CreditedQuantity = $CreditedQuantity;
        return $this;
    }

    public function getLineExtensionAmount()
    {
        return $this->LineExtensionAmount;
    }

    public function setLineExtensionAmount($LineExtensionAmount)
    {
        $this->LineExtensionAmount = $LineExtensionAmount;
        return $this;
    }
    public function getPricingReference()
    {
        return $this->PricingReference;
    }

    public function setPricingReference(PricingReference $PricingReference)
    {
        $this->PricingReference = $PricingReference;
        return $this;
    }

    public function getTaxTotal()
    {
        return $this->TaxTotal;
    }

    public function setTaxTotal(TaxTotal $TaxTotal)
    {
        $this->TaxTotal = $TaxTotal;
        return $this;
    }

    public function getItem()
    {
        return $this->Item;
    }

    public function setItem(Item $Item)
    {
        $this->Item = $Item;
        return $this;
    }

    public function getPrice()
    {
        return $this->Price;
    }

    public function setPrice(Price $Price)
    {
        $this->Price = $Price;
        return $this;
    }

}
