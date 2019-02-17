<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat;

use InvalidArgumentException;
use F72X\F72X;
use F72X\Company;
use F72X\Exception\ConfigException;

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
    const CAT_PERCEPCION_REGIME    = 22;
    const CAT_RETENCION_REGIME     = 23;
    const CAT_FACTURA_TYPE         = 51;

    /** @CAT1 Código tipo de documento */
    const DOCTYPE_FACTURA = '01';
    const DOCTYPE_BOLETA = '03';
    const DOCTYPE_NOTA_CREDITO = '07';
    const DOCTYPE_NOTA_DEBITO = '08';
    const DOCTYPE_VOUCHER = '12';
    const DOCTYPE_SC_FACTURA = 'FAC';
    const DOCTYPE_SC_BOLETA = 'BOL';
    const DOCTYPE_SC_NOTA_CREDITO = 'NCR';
    const DOCTYPE_SC_NOTA_DEBITO = 'NDE';

    /** @CAT5 Tipo de impuesto */
    const CAT5_IGV = '1000';
    const CAT5_IVAP = '1016';
    const CAT5_ISC = '2000';
    const CAT5_EXP = '9995';
    const CAT5_GRA = '9996';
    const CAT5_EXO = '9997';
    const CAT5_INA = '9998';
    const CAT5_OTROS = '9999';

    /** @CAT7 Tipo de afectación del IGV */
    const CAT7_GRA_IGV = '10';

    /** @CAT16 Tipo de precio */
    const CAT16_UNITARY_PRICE = '01';
    const CAT16_REF_VALUE = '02';

    /** @CAT6 */
    const IDENTIFICATION_DOC_DNI = '1';
    const IDENTIFICATION_DOC_RUC = '6';

    private static $_CAT = [];
    private static $_LIST = [];

    /**
     * 
     * @param string $documentType 01|03|07|08
     * @param string $affectedDocumentType 01|03
     * @return string F|B
     */
    public static function getDocumentSeriesPrefix($documentType, $affectedDocumentType = null) {
        if ($documentType == self::DOCTYPE_FACTURA) {
            return 'F';
        }
        if ($documentType == self::DOCTYPE_BOLETA) {
            return 'B';
        }
        if ($documentType == self::DOCTYPE_NOTA_CREDITO) {
            return self::getDocumentSeriesPrefix($affectedDocumentType);
        }
        if ($documentType == self::DOCTYPE_NOTA_DEBITO) {
            return self::getDocumentSeriesPrefix($affectedDocumentType);
        }
        if ($documentType == self::DOCTYPE_VOUCHER) {
            throw new InvalidArgumentException("Error: Cash register ticket isn't supported yet.");
        }
        throw new InvalidArgumentException("Error: $documentType isn't a valid document type");
    }

    /**
     * 
     * @param string $documentType 01|03|07|08
     * @return string FACTURA|BOLETA|NOTA DE CRÉDITO|NOTA DE DÉBITO
     */
    public static function getOfficialDocumentName($documentType) {
        switch ($documentType) {
            case self::DOCTYPE_FACTURA : return 'FACTURA';
            case self::DOCTYPE_BOLETA : return 'BOLETA DE VENTA';
            case self::DOCTYPE_NOTA_CREDITO : return 'NOTA DE CRÉDITO';
            case self::DOCTYPE_NOTA_DEBITO : return 'NOTA DE DÉBITO';
        }
        throw new InvalidArgumentException("Error: $documentType isn't a valid document type");
    }

    /**
     * 
     * @param string $shortCode BOL|FAC|NCR|NDE
     * @return string 01|03|07|08
     */
    public static function getDocumentType($shortCode) {
        switch ($shortCode) {
            case self::DOCTYPE_SC_FACTURA: return self::DOCTYPE_FACTURA;
            case self::DOCTYPE_SC_BOLETA: return self::DOCTYPE_BOLETA;
            case self::DOCTYPE_SC_NOTA_CREDITO: return self::DOCTYPE_NOTA_CREDITO;
            case self::DOCTYPE_SC_NOTA_DEBITO: return self::DOCTYPE_NOTA_DEBITO;
        }
        throw new InvalidArgumentException("Error: $shortCode isn't valid short code");
    }

    public static function itemExist($catNumber, $itemID) {
        $items = self::getCatItems($catNumber);
        return key_exists($itemID, $items);
    }

    /**
     * Valor de item de catálogo
     * @param int $catNumber
     * @param string $itemID
     * @return string
     * @throws InvalidArgumentException cuando el item no existe en el catálogo.
     */
    public static function getCatItemValue($catNumber, $itemID) {
        $item = self::getCatItem($catNumber, $itemID);
        if ($item) {
            return $item['value'];
        }
        throw new InvalidArgumentException("Catálogo Error, no existe un item con ID='$itemID' en el cátalogo='$catNumber'");
    }

    /**
     * Valor de item de catálogo
     * @param int $catNumber
     * @param string $itemID
     * @return string
     * @throws InvalidArgumentException cuando el item no existe en el catálogo.
     */
    public static function getCatItemFieldValue($catNumber, $itemID, $field) {
        $item = self::getCatItem($catNumber, $itemID);
        if ($item) {
            if(isset($item[$field])){
                return $item[$field];
            }
            throw new InvalidArgumentException("Catálogo Error, no existe el campo '$field' en el cátalogo='$catNumber'");
        }
        throw new InvalidArgumentException("Catálogo Error, no existe un item con ID='$itemID' en el cátalogo='$catNumber'");
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

    public static function getCatItems($catNumber, $useXmlFiles = false) {
        // returns from cache
        if (isset(self::$_CAT['CAT_' . $catNumber])) {
            return self::$_CAT['CAT_' . $catNumber];
        }
        // Load from file
        $fileName = strtoupper(self::getCatFileName($catNumber));
        $items = require SunatVars::DIR_CATS . "/$fileName.php";
        self::$_CAT['CAT_' . $catNumber] = $items;
        return $items;
    }

    private static function getCatFileName($catNumeber) {
        return 'cat_' . str_pad($catNumeber, 2, '0', STR_PAD_LEFT);
    }

    public static function getCurrencyPlural($currencyCode) {
        $currencies = self::getCustomList('currencies');
        if (isset($currencies[$currencyCode])) {
            return $currencies[$currencyCode];
        }
        throw new ConfigException("El código de moneda $currencyCode aún no ha sido configurado para su uso.");
    }

    public static function getUnitName($unitCode) {
        return self::getCustomListItem('unitcode', $unitCode);
    }

    public static function getCustomListItem($listName, $itemId) {
        $customList = self::getCustomList($listName);
        if (isset($customList[$itemId])) {
            return $customList[$itemId];
        }
        throw new ConfigException("El codigó de item '$itemId' no existe en la lista $listName");
    }

    public static function getCustomList($listName) {
        // returns from cache
        if (isset(self::$_LIST['LIST_' . $listName])) {
            return self::$_LIST['LIST_' . $listName];
        }
        // Company customization
        $customListsPath = Company::getListsPath();
        $fileName = "$customListsPath/$listName.php";
        if (file_exists($fileName)) {
            $customListsPath = Company::getListsPath();
            $list = require $fileName;
            // Cache
            self::$_CAT['LIST_' . $listName] = $list;
            return $list;
        }
        // Defaults
        $defaultListsPath = F72X::getDefaultListsPath();
        $fileName2 = "$defaultListsPath/$listName.php";
        $list = require $fileName2;
        // Cache
        self::$_CAT['LIST_' . $listName] = $list;
        return $list;
    }

    public static function getAllCatNumbers() {
        $catNumbers = [];
        for ($i = 1; $i <= 3; $i++) {
            $catNumbers[] = $i;
        }
        for ($i = 5; $i <= 9; $i++) {
            $catNumbers[] = $i;
        }
        for ($i = 10; $i <= 11; $i++) {
            $catNumbers[] = $i;
        }
        for ($i = 13; $i <= 27; $i++) {
            $catNumbers[] = $i;
        }
        for ($i = 51; $i <= 59; $i++) {
            $catNumbers[] = $i;
        }
        return $catNumbers;
    }

}
