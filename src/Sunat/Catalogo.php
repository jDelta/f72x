<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat;

use DOMDocument;
use Sabre\Xml\Reader;

class Catalogo {

    /** @CAT1 Código tipo de documento */
    const CAT1_FACTURA      = '01';
    const CAT1_BOLETA       = '03';
    const CAT1_NOTA_CREDITO = '07';
    const CAT1_NOTA_DEBITO  = '08';

    /** @CAT5 Tipo de impuesto*/
    const CAT5_IGV   = '1000';
    const CAT5_IVAP  = '1016';
    const CAT5_ISC   = '2000';
    const CAT5_EXP   = '9995';
    const CAT5_GRA   = '9996';
    const CAT5_EXO   = '9997';
    const CAT5_INA   = '9998';
    const CAT5_OTROS = '9999';

    /** @CAT7 Tipo de afectación del IGV */
    const CAT7_GRA_IGV        = '10';
    /** @CAT16 Tipo de precio */
    const CAT16_UNITARY_PRICE = '01';
    const CAT16_REF_VALUE     = '02';

    /** @CAT6*/
    const IDENTIFICATION_DOC_DNI = '1';
    const IDENTIFICATION_DOC_RUC = '6';

    private static $_CAT = [];

    public static function getCatItem($catNumber, $itemID, $key = 'id') {
        $items = self::getCatItems($catNumber);
        foreach ($items as $item) {
            if ($item[$key] === strval($itemID)) {
                return $item;
            }
        }
        return null;
    }

    public static function getCatItems($catNumber) {
        // returns from cache
        if (isset(self::$_CAT['CAT_' . $catNumber])) {
            return self::$_CAT['CAT_' . $catNumber];
        }
        // Load the XML
        $xmlName = self::getXmlName($catNumber);
        $doc = new DOMDocument();
        $doc->load(SunatVars::DIR_CATS . "/$xmlName");

        $reader = new Reader();
        $reader->xml($doc->saveXML());

        $catData = $reader->parse();
        $items = $catData['value'];
        foreach ($items as &$item) {
            unset($item['name']); // Here because the item may contain the name attribute!
            foreach ($item['attributes'] as $attKey => $att) {
                $item[$attKey] = $att;
            }
            unset($item['attributes']);
        }
        // Cache
        self::$_CAT['CAT_' . $catNumber] = $items;
        return self::$_CAT['CAT_' . $catNumber];
    }

    private static function getXmlName($catNumeber) {
        return 'cat_' . str_pad($catNumeber, 2, '0', STR_PAD_LEFT) . '.xml';
    }

}
