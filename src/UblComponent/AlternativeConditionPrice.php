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

class AlternativeConditionPrice extends BaseComponent
{

    const DECIMALS = 2;

    protected $currencyID;
    protected $PriceAmount;
    protected $PriceTypeCode; //@CAT16
    protected $validations = [
        'currencyID',
        'PriceTypeCode',
        'PriceAmount'
    ];

    function xmlSerialize(Writer $writer): void
    {
        $this->validate();
        $writer->write([
            [
                'name' => SchemaNS::CBC . 'PriceAmount',
                'value' => Operations::formatAmount($this->PriceAmount, self::DECIMALS),
                'attributes' => [
                    'currencyID' => $this->currencyID
                ],
            ],
            [
                'name' => SchemaNS::CBC . 'PriceTypeCode',
                'value' => $this->PriceTypeCode,
                'attributes' => [
                    'listName' => 'SUNAT:Indicador de Tipo de Precio',
                    'listAgencyName' => 'PE:SUNAT',
                    'listURI' => 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16'
                ]
            ]
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

    public function getPriceAmount()
    {
        return $this->PriceAmount;
    }

    public function setPriceAmount($PriceAmount)
    {
        $this->PriceAmount = $PriceAmount;
        return $this;
    }

    public function getPriceTypeCode()
    {
        return $this->PriceTypeCode;
    }

    /**
     *
     * @param string $PriceTypeCode @CAT16
     * @return $this
     */
    public function setPriceTypeCode($PriceTypeCode)
    {
        $this->PriceTypeCode = $PriceTypeCode;
        return $this;
    }

}
