<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X;

use ZipArchive;
use F72X\F72X;
use F72X\Tools\ZipFile;
use F72X\Exception\FileException;

class Repository {

    public static function saveDocument($documentName, $billContent) {
        self::saveFile("xml/$documentName.xml", $billContent);
    }

    /**
     * 
     * @param string $documentName
     * @param boolean $throwEx set to *false*, to avoid an exception if the file doesn't exist
     */
    public static function removeFiles($documentName, $throwEx = true) {
        $rp = self::getRepositoryPath();
        self::removeFile("$rp/xml/$documentName.xml", $throwEx);
        self::removeFile("$rp/input/$documentName.json", $throwEx);
        self::removeFile("$rp/sxml/S-$documentName.xml", $throwEx);
        self::removeFile("$rp/zip/$documentName.zip", $throwEx);
        self::removeFile("$rp/pdf/$documentName.pdf", $throwEx);
        self::removeFile("$rp/ticket/$documentName.zip", $throwEx);
        self::removeFile("$rp/cdr/R$documentName.zip", $throwEx);
    }

    public static function saveDocumentInput($documentName, $billContent) {
        self::saveFile("input/$documentName.json", $billContent);
    }

    public static function saveSignedXml($documentName, $billContent) {
        self::saveFile("sxml/S-$documentName.xml", $billContent);
    }

    public static function saveTicket($documentName, $ticket) {
        self::saveFile("ticket/$documentName.json", json_encode(['ticket' => $ticket]), true);
    }

    public static function saveCdr($documentName, $billContent) {
        self::saveFile("cdr/R$documentName.zip", $billContent, true);
    }

    public static function savePDF($documentName, $fileContent) {
        self::saveFile("pdf/$documentName.pdf", $fileContent);
    }

    public static function zipDocument($documentName) {
        $rp = self::getRepositoryPath();
        $re = F72X::getRunningEnvironment();
        $zipPath = "$rp/zip/$documentName.zip";
        $xmlPath = "$rp/sxml/S-$documentName.xml";
        $xmlFileName = "$documentName.xml";
        if ($re === F72X::RUNNING_ENV_GAE || $re === F72X::RUNNING_ENV_GAE_DEV_SERVER) {
            $zip = new ZipFile();
            $zip->addFile(file_get_contents($xmlPath), $xmlFileName);
            file_put_contents($zipPath, $zip->file());
        } else {
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($xmlPath, $xmlFileName);
                $zip->close();
            }
        }
    }

    public static function billExist($documentName) {
        $rp = self::getRepositoryPath();
        return fileExists("$rp/xml/$documentName.xml");
    }

    public static function cdrExist($documentName) {
        $rp = self::getRepositoryPath();
        return fileExists("$rp/cdr/R$documentName.zip");
    }

    public static function getRepositoryPath() {
        return Company::getRepositoryPath();
    }

    public static function getXmlPath($documentName) {
        $rp = self::getRepositoryPath();
        return "$rp/xml/$documentName.xml";
    }

    public static function getBillInputPath($documentName) {
        $rp = self::getRepositoryPath();
        return "$rp/input/$documentName.json";
    }

    public static function getSignedBillPath($documentName) {
        $rp = self::getRepositoryPath();
        return "$rp/sxml/S-$documentName.xml";
    }

    public static function getZippedBillPath($documentName) {
        $rp = self::getRepositoryPath();
        return "$rp/zip/$documentName.zip";
    }

    public static function getDocumentTicketPath($documentName) {
        $rp = self::getRepositoryPath();
        return "$rp/ticket/$documentName.json";
    }

    public static function getZipContent($documentName) {
        return file_get_contents(self::getZippedBillPath($documentName));
    }

    public static function getDocumentTicketContent($documentName) {
        return file_get_contents(self::getDocumentTicketPath($documentName));
    }

    public static function getPdfPath($documentName) {
        $rp = self::getRepositoryPath();
        return "$rp/pdf/$documentName.pdf";
    }

    private static function saveFile($filePath, $fileContent, $overwrite = false) {
        $rp = self::getRepositoryPath();
        self::writeFile("$rp/$filePath", $fileContent, $overwrite);
    }

    public static function removeFile($filePath, $throwEx = true) {
        if (!file_exists($filePath)) {
            if ($throwEx) {
                throw new FileException("El archivo: $filePath no existe.");
            }
            return;
        }
        unlink($filePath);
    }

    public static function getTicketInfo($documentName) {
        $ticketContent = self::getDocumentTicketContent($documentName);
        return json_decode($ticketContent, true);
    }

    public static function getCdrInfo($documentName) {
        $rp = self::getRepositoryPath();
        $zip = new ZipArchive();
        if ($zip->open("$rp/cdr/R$documentName.zip") === true) {
            $xmlString = $zip->getFromName("R-$documentName.xml");
            $info = self::getMapCdr($xmlString);
            $zip->close();
            return $info;
        } else {
            throw new FileException("No se encontró el archivo R$documentName.zip");
        }
    }

    private static function getMapCdr($xmlString) {
        $xmlStringI1 = str_replace(['ar:', 'ext:', 'cac:', 'cbc:'], '', $xmlString);
        $SimpleXml = simplexml_load_string($xmlStringI1);
        $origin = json_decode(json_encode($SimpleXml), 1);
        $respNode = $origin['DocumentResponse'];
        return [
            'id' => $origin['ID'],
            'documentId' => $respNode['DocumentReference']['ID'],
            'receiverId' => $respNode['RecipientParty']['PartyIdentification']['ID'],
            'issueDate' => $origin['IssueDate'],
            'issueTime' => $origin['IssueTime'],
            'responseDate' => $origin['ResponseDate'],
            'responseTime' => $origin['ResponseTime'],
            'responseCode' => $respNode['Response']['ResponseCode'],
            'responseDesc' => $respNode['Response']['Description']
        ];
    }

    public static function xmlStream($documentName) {
        $filePath = self::getXmlPath($documentName);
        if (file_exists($filePath)) {
            header('Content-Type: application/xml');
            header("Content-Disposition: attachment;filename=$documentName.xml");
            header('Cache-Control:max-age=0');
            echo file_get_contents($filePath);
            exit();
        }
        throw new FileException("El archivo: $filePath no existe.");
    }

    public static function signedXmlStream($documentName) {
        $filePath = self::getSignedBillPath($documentName);
        if (file_exists($filePath)) {
            header('Content-Type: application/xml');
            header("Content-Disposition: attachment;filename=S-$documentName.xml");
            header('Cache-Control:max-age=0');
            echo file_get_contents($filePath);
            exit();
        }
        throw new FileException("El archivo: $filePath no existe.");
    }

    public static function getPdfStream($documentName) {
        $filePath = self::getPdfPath($documentName);
        if (file_exists($filePath)) {
            return file_get_contents($filePath);
        }
        throw new FileException("El archivo: $filePath no existe.");
    }

    public static function pdfStream($documentName, $browserView = false) {
        $stream = self::getPdfStream($documentName);
        header('Content-Type: application/pdf');
        if (!$browserView) {
            header("Content-Disposition: attachment;filename=$documentName.pdf");
        }
        header('Cache-Control:max-age=0');
        echo $stream;
        exit();
    }

    public static function billInputStream($documentName) {
        $filePath = self::getBillInputPath($documentName);
        if (file_exists($filePath)) {
            header('Content-Type: application/json');
            header("Content-Disposition: attachment;filename=$documentName.json");
            header('Cache-Control:max-age=0');
            echo file_get_contents($filePath);
            exit();
        }
        throw new FileException("El archivo: $filePath no existe.");
    }

    public static function writeFile($filePath, $fileContent, $overwrite = false) {
        if (file_exists($filePath) && !$overwrite) {
            throw new FileException("El archivo: $filePath ya existe.");
        }
        file_put_contents($filePath, $fileContent);
    }

}
