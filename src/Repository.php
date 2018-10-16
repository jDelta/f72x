<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X;

use ZipArchive;
use F72X\Company;
use F72X\Exception\FileException;

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

    public static function savePDF($billName, $fileContent) {
        self::saveFile("printable/$billName.pdf", $fileContent);
    }

    public static function zipBill($billName) {
        $rp = self::getRepositoryPath();
        $zip = new ZipArchive();
        if ($zip->open("$rp/zippedbill/$billName.zip", ZipArchive::CREATE) === TRUE) {
            $zip->addFile("$rp/signedbill/S-$billName.xml", "$billName.xml");
            $zip->close();
        }
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

    public static function getCdrInfo($billName) {
        $rp = self::getRepositoryPath();
        $zip = new ZipArchive();
        $info = null;
        if ($zip->open("$rp/cdr/R$billName.zip") === true) {
            $xmlString = $zip->getFromName("R-$billName.xml");
            $info = self::getMapCdr($xmlString);
            $zip->close();
        } else {
            throw new FileException("No se encontró el archivo R$billName.zip");
        }
        return $info;
    }

    private static function getMapCdr($xmlString) {
        $xmlStringI1 = str_replace(['ar:', 'ext:', 'cac:', 'cbc:'], '', $xmlString);
        $SimpleXml = simplexml_load_string($xmlStringI1);
        $origin = json_decode(json_encode($SimpleXml), 1);
        $respNode = $origin['DocumentResponse'];
        return [
            'id' => $origin['ID'],
            'invoiceId' => $respNode['DocumentReference']['ID'],
            'receiverId' => $respNode['RecipientParty']['PartyIdentification']['ID'],
            'issueDate' => $origin['IssueDate'],
            'issueTime' => $origin['IssueTime'],
            'responseDate' => $origin['ResponseDate'],
            'responseTime' => $origin['ResponseTime'],
            'responseCode' => $respNode['Response']['ResponseCode'],
            'responseDesc' => $respNode['Response']['Description']
        ];
    }

}
