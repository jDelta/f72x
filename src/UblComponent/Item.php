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
use Sabre\Xml\Element\Cdata;

class Item extends BaseComponent {

    protected $Description;

    /** @var SellersItemIdentification */
    protected $SellersItemIdentification;

    /** @var CommodityClassification */
    protected $CommodityClassification;
    protected $validations = [
        'Description'
    ];

    function xmlSerialize(Writer $writer) {
        $this->validate();
        $writer->write([
            SchemaNS::CBC . 'Description'                   => new Cdata($this->Description)
        ]);
        if ($this->SellersItemIdentification) {
            $writer->write([
                SchemaNS::CAC . 'SellersItemIdentification' => $this->SellersItemIdentification
            ]);
        }
        if ($this->CommodityClassification) {
            $writer->write([
                SchemaNS::CAC . 'CommodityClassification'   => $this->CommodityClassification
            ]);
        }
    }

    public function getDescription() {
        return $this->Description;
    }

    public function setDescription($Description) {
        $this->Description = $Description;
        return $this;
    }

    public function getSellersItemIdentification() {
        return $this->SellersItemIdentification;
    }

    public function setSellersItemIdentification(SellersItemIdentification $SellersItemIdentification) {
        $this->SellersItemIdentification = $SellersItemIdentification;
        return $this;
    }

    public function getCommodityClassification() {
        return $this->CommodityClassification;
    }

    public function setCommodityClassification(CommodityClassification $CommodityClassification) {
        $this->CommodityClassification = $CommodityClassification;
        return $this;
    }

}
