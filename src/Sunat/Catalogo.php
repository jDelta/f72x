<?php

/**
 * FACTURA ELECTRÓNICA SUNAT
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat;

use DOMDocument;
use Sabre\Xml\Reader;

class Catalogo {

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
