<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X;

use F72X\Company;

class Repository {

    public static function saveBill($billName, $billContent) {
        self::saveFile("bill/$billName.xml", $billContent);
    }

    public static function saveSignedBill($billName, $billContent) {
        self::saveFile("signedbill/S-$billName.xml", $billContent);
    }

    public static function saveCdr($billName, $billContent) {
        self::saveFile("cdr/R$billName.zip", $billContent);
    }

    public static function billExist($billName) {
        $rp = self::getRepositoryPath();
        return fileExists("$rp/bill/$billName.xml");
    }

    public static function cdrExist($billName) {
        $rp = self::getRepositoryPath();
        return fileExists("$rp/cdr/R$billName.zip");
    }

    public static function getRepositoryPath() {
        return Company::getRepositoryPath();
    }

    public static function getBillPath($billName) {
        $rp = self::getRepositoryPath();
        return "$rp/bill/$billName.xml";
    }

    private static function saveFile($filePath, $fileContent) {
        $rp = self::getRepositoryPath();
        file_put_contents("$rp/$filePath", $fileContent);
    }

}
