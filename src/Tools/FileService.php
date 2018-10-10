<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Tools;

use ZipArchive;
use F72X\Company;
use F72X\Exception\FileException;

class FileService {

    public static function doZip($billName) {
        $fileDir = Company::getRepositoryPath();
        $zipFilename = explode('.', $billName)[0] . '.zip';
        $zip = new ZipArchive();
        if ($zip->open("$fileDir/zippedbill/$zipFilename", ZipArchive::CREATE) === TRUE) {
            $zip->addFile("$fileDir/signedbill/S-$billName", $billName);
            $zip->close();
        }
    }

    public static function getBase64($filePath) {
        return base64_encode(file_get_contents($filePath));
    }

    public static function getCdrInfo($billName) {
        $repository = Company::getRepositoryPath();
        $zip = new ZipArchive();
        $info = null;
        if ($zip->open("$repository/cdr/R$billName.zip") === true) {
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
            'invoiceId'    => $respNode['DocumentReference']['ID'],
            'receiverId'   => $respNode['RecipientParty']['PartyIdentification']['ID'],
            'issueDate'    => $origin['IssueDate'],
            'issueTime'    => $origin['IssueTime'],
            'responseDate' => $origin['ResponseDate'],
            'responseTime' => $origin['ResponseTime'],
            'responseCode' => $respNode['Response']['ResponseCode'],
            'responseDesc' => $respNode['Response']['Description']
        ];
    }

}
