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

class AllowanceCharge extends BaseComponent {

    const DECIMALS = 2;

    protected $currencyID;

    /**
     * 
     * @var string True|False
     * True: Para cargos
     * False: Para descuentos
     */
    protected $ChargeIndicator;

    /**
     *
     * @var string
     * Catálogo #53
     *     00: OTROS DESCUENTOS
     *     [50-55]: CARGOS
     */
    protected $AllowanceChargeReasonCode;

    /** @var float */
    protected $MultiplierFactorNumeric;

    /** @var float */
    protected $Amount;

    /** @var float */
    protected $BaseAmount;
    protected $validations = [
        'currencyID',
        'ChargeIndicator',
        'AllowanceChargeReasonCode',
        'Amount',
        'BaseAmount'
    ];

    function xmlSerialize(Writer $writer) {
        $me = $this;
        $me->validate();
        $writer->write([
            SchemaNS::CBC . 'ChargeIndicator'           => $me->ChargeIndicator,
            SchemaNS::CBC . 'AllowanceChargeReasonCode' => $me->AllowanceChargeReasonCode
        ]);
        if ($me->MultiplierFactorNumeric) {
            $writer->write([
                SchemaNS::CBC . 'MultiplierFactorNumeric' => Operations::formatAmount($this->MultiplierFactorNumeric, self::DECIMALS),
            ]);
        }
        $writer->write([
            [
                'name' => SchemaNS::CBC . 'Amount',
                'value' => Operations::formatAmount($this->Amount, self::DECIMALS),
                'attributes' => [
                    'currencyID' => $me->currencyID
                ]
            ],
            [
                'name' => SchemaNS::CBC . 'BaseAmount',
                'value' => Operations::formatAmount($this->BaseAmount, self::DECIMALS),
                'attributes' => [
                    'currencyID' => $me->currencyID
                ]
            ]
        ]);
    }

    public function getCurrencyID() {
        return $this->currencyID;
    }

    public function setCurrencyID($currencyID) {
        $this->currencyID = $currencyID;
        return $this;
    }

    public function getChargeIndicator() {
        return $this->ChargeIndicator;
    }

    /**
     * 
     * @param string $ChargeIndicator True|False
     *     True: Para cargos
     *     False: Para descuentos
     * @return $this
     */
    public function setChargeIndicator($ChargeIndicator) {
        $this->ChargeIndicator = $ChargeIndicator;
        return $this;
    }

    public function getAllowanceChargeReasonCode() {
        return $this->AllowanceChargeReasonCode;
    }

    public function setAllowanceChargeReasonCode($AllowanceChargeReasonCode) {
        $this->AllowanceChargeReasonCode = $AllowanceChargeReasonCode;
        return $this;
    }

    public function getMultiplierFactorNumeric() {
        return $this->MultiplierFactorNumeric;
    }

    public function setMultiplierFactorNumeric($MultiplierFactorNumeric) {
        $this->MultiplierFactorNumeric = $MultiplierFactorNumeric;
        return $this;
    }

    public function getAmount() {
        return $this->Amount;
    }

    public function setAmount($Amount) {
        $this->Amount = $Amount;
        return $this;
    }

    public function getBaseAmount() {
        return $this->BaseAmount;
    }

    public function setBaseAmount($BaseAmount) {
        $this->BaseAmount = $BaseAmount;
        return $this;
    }

}
