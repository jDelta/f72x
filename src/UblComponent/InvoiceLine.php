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

class InvoiceLine extends BaseComponent {

    const DECIMALS = 2;

    protected $currencyID;
    protected $ID;
    protected $unitCode = 'NIU';
    protected $InvoicedQuantity;
    protected $LineExtensionAmount;

    /** @var PricingReference */
    protected $PricingReference;

    /** @var AllowanceCharge[] */
    protected $AllowanceCharges = [];

    /** @var TaxTotal */
    protected $TaxTotal;

    /** @var Item */
    protected $Item;

    /** @var Price */
    protected $Price;

    protected $validations = [
        'currencyID',
        'ID',
        'InvoicedQuantity',
        'unitCode',
        'LineExtensionAmount',
        'PricingReference',
        'TaxTotal',
        'Item',
        'Price'
    ];

    function xmlSerialize(Writer $writer) {
        $this->validate();
        $writer->write([
            SchemaNS::CBC . 'ID' => $this->ID,
            [
                'name'          => SchemaNS::CBC . 'InvoicedQuantity',
                'value'         => $this->InvoicedQuantity,
                'attributes'    => [
                    'unitCode'                  => $this->unitCode,
                    'unitCodeListID'            => 'UN/ECE rec 20',
                    'unitCodeListAgencyName'    => 'United Nations Economic Commission for Europe'
                ]
            ],
            [
                'name'          => SchemaNS::CBC . 'LineExtensionAmount',
                'value'         => Operations::formatAmount($this->LineExtensionAmount, self::DECIMALS),
                'attributes'    => [
                    'currencyID' => $this->currencyID
                ]
            ],
            SchemaNS::CAC . 'PricingReference' => $this->PricingReference
        ]);
        // Cargos y descuentos
        foreach ($this->AllowanceCharges as $AllowanceCharge) {
            $writer->write([
                SchemaNS::CAC . 'AllowanceCharge' => $AllowanceCharge
            ]);
        }
        $writer->write([
            SchemaNS::CAC . 'TaxTotal'  => $this->TaxTotal,
            SchemaNS::CAC . 'Item'      => $this->Item,
            SchemaNS::CAC . 'Price'     => $this->Price
        ]);

    }

    public function getCurrencyID() {
        return $this->currencyID;
    }

    public function setCurrencyID($currencyID) {
        $this->currencyID = $currencyID;
        return $this;
    }

    public function getID() {
        return $this->ID;
    }

    public function setID($ID) {
        $this->ID = $ID;
        return $this;
    }
    public function getUnitCode() {
        return $this->unitCode;
    }

    public function setUnitCode($unitCode) {
        $this->unitCode = $unitCode;
        return $this;
    }

    public function getInvoicedQuantity() {
        return $this->InvoicedQuantity;
    }

    public function setInvoicedQuantity($InvoicedQuantity) {
        $this->InvoicedQuantity = $InvoicedQuantity;
        return $this;
    }

    public function getLineExtensionAmount() {
        return $this->LineExtensionAmount;
    }

    public function setLineExtensionAmount($LineExtensionAmount) {
        $this->LineExtensionAmount = $LineExtensionAmount;
        return $this;
    }
    public function getPricingReference() {
        return $this->PricingReference;
    }

    public function setPricingReference(PricingReference $PricingReference) {
        $this->PricingReference = $PricingReference;
        return $this;
    }
    public function getAllowanceCharges() {
        return $this->AllowanceCharges;
    }

    public function setAllowanceCharges(array $AllowanceCharges) {
        $this->AllowanceCharges = $AllowanceCharges;
        return $this;
    }

    /**
     * 
     * @param AllowanceCharge $AllowanceCharge
     * @return $this
     */
    public function addAllowanceCharge(AllowanceCharge $AllowanceCharge) {
        $this->AllowanceCharges[] = $AllowanceCharge;
        return $this;
    }

    public function getTaxTotal() {
        return $this->TaxTotal;
    }

    public function setTaxTotal(TaxTotal $TaxTotal) {
        $this->TaxTotal = $TaxTotal;
        return $this;
    }

    public function getItem() {
        return $this->Item;
    }

    public function setItem(Item $Item) {
        $this->Item = $Item;
        return $this;
    }

    public function getPrice() {
        return $this->Price;
    }

    public function setPrice(Price $Price) {
        $this->Price = $Price;
        return $this;
    }

}
