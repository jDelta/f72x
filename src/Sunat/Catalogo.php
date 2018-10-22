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
use F72X\Company;
use F72X\Exception\ConfigException;
use Sabre\Xml\Reader;

class Catalogo {

    const CAT_TAX_DOCUMENT_TYPE    = 1;
    const CAT_CURRENCY_TYPE        = 2;
    const CAT_MEASUREMENT_UNIT     = 3;
    const CAT_COUNTRY_CODE         = 4;
    const CAT_TAX_TYPE             = 5;
    const CAT_IDENT_DOCUMENT_TYPE  = 6;
    const CAT_IGV_AFFECTATION_TYPE = 7;
    const CAT_ISC_CALC_SYSTEM_TYPE = 8;
    const CAT_NOTA_CREDITO_TYPE    = 9;
    const CAT_NOTA_DEBITO_TYPE     = 10;
    const CAT_FACTURA_TYPE         = 51;
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
    private static $_LIST = [];
    public static function itemExist($catNumber, $itemID) {
        $items = self::getCatItems($catNumber);
        return key_exists($itemID, $items);
    }
    
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
        $itemsO = [];
        foreach ($items as &$item) {
            unset($item['name']); // Here because the item may contain the name attribute!
            foreach ($item['attributes'] as $attKey => $att) {
                $item[$attKey] = $att;
            }
            unset($item['attributes']);
            $itemsO[$item['id']] = $item;
        }
        // Cache
        self::$_CAT['CAT_' . $catNumber] = $itemsO;
        return self::$_CAT['CAT_' . $catNumber];
    }

    private static function getXmlName($catNumeber) {
        return 'cat_' . str_pad($catNumeber, 2, '0', STR_PAD_LEFT) . '.xml';
    }

    public static function getCurrencyPlural($currencyType) {
        $currencies = self::getCustomList('currencies');
        if(isset($currencies[$currencyType])){
            return $currencies[$currencyType];
        }
        throw new ConfigException("El tipo de moneda $currencyType aún no ha sido configurado para su uso.");
    }

    public static function getUnitName($unitCode) {
        return self::getCustomListItem('unitcode', $unitCode);
    }

    public static function getCustomListItem($listName, $itemId) {
        $customList = self::getCustomList($listName);
        if(isset($customList[$itemId])){
            return $customList[$itemId];
        }
        throw new ConfigException("El codigó de item $itemId no existe en la lista $listName");
    }
    public static function getCustomList($listName) {
        // returns from cache
        if (isset(self::$_LIST['LIST_' . $listName])) {
            return self::$_LIST['LIST_' . $listName];
        }
        $path = Company::getListsPath();
        $fileName = "$path/$listName.php";
        if(file_exists($fileName)){
            $path = Company::getListsPath();
            $list = require $fileName;
            // Cache
            self::$_CAT['LIST_' . $listName] = $list;
            return $list;
        }
        throw new ConfigException("No se encontró el archivo $listName.php dentro de su directorio de listas personalizadas.");
    }

    public static function catItemsToPhpArray($catNumber, $resultPath) {
        $items = Catalogo::getCatItems($catNumber);
        $lines = [];
        $ENTER = chr(13) . chr(10);
        foreach ($items as $item) {
            $lines[] = "    ['id' => '" . $item['id'] . "', 'value' => '" . $item['value'] . "']";
        }
        $joinedLines = implode(',' . $ENTER, $lines);
        $result = "[" . $ENTER . $joinedLines . $ENTER . "]";
        file_put_contents($resultPath, $result);
    }

}
