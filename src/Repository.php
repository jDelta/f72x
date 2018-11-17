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

    /**
     * 
     * @param string $documentName
     * @param boolean $throwEx set to *false*, to avoid an exception if the file doesn't exist
     */
    public static function removeBillDocs($documentName, $throwEx = true) {
        $rp = self::getRepositoryPath();
        self::removeFile("$rp/bill/$documentName.xml", $throwEx);
        self::removeFile("$rp/billinput/$documentName.json", $throwEx);
        self::removeFile("$rp/signedbill/S-$documentName.xml", $throwEx);
        self::removeFile("$rp/zippedbill/$documentName.zip", $throwEx);
        self::removeFile("$rp/printable/$documentName.pdf", $throwEx);
        self::removeFile("$rp/ticket/$documentName.zip", $throwEx);
        self::removeFile("$rp/cdr/R$documentName.zip", $throwEx);
    }

    public static function saveBillInput($billName, $billContent) {
        self::saveFile("billinput/$billName.json", $billContent);
    }

    public static function saveSignedBill($billName, $billContent) {
        self::saveFile("signedbill/S-$billName.xml", $billContent);
    }

    public static function saveTicket($documentName, $ticket) {
        self::saveFile("ticket/$documentName.json", json_encode(['ticket' => $ticket]), true);
    }

    public static function saveCdr($billName, $billContent) {
        self::saveFile("cdr/R$billName.zip", $billContent, true);
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

    public static function getBillInputPath($billName) {
        $rp = self::getRepositoryPath();
        return "$rp/billinput/$billName.json";
    }

    public static function getSignedBillPath($billName) {
        $rp = self::getRepositoryPath();
        return "$rp/signedbill/S-$billName.xml";
    }

    public static function getZippedBillPath($billName) {
        $rp = self::getRepositoryPath();
        return "$rp/zippedbill/$billName.zip";
    }

    public static function getDocumentTicketPath($documentName) {
        $rp = self::getRepositoryPath();
        return "$rp/ticket/$documentName.json";
    }

    public static function getZippedBillContent($billName) {
        return file_get_contents(self::getZippedBillPath($billName));
    }

    public static function getDocumentTicketContent($documentName) {
        return file_get_contents(self::getDocumentTicketPath($documentName));
    }

    public static function getPdfPath($billName) {
        $rp = self::getRepositoryPath();
        return "$rp/printable/$billName.pdf";
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

    public static function getCdrInfo($billName) {
        $rp = self::getRepositoryPath();
        $zip = new ZipArchive();
        if ($zip->open("$rp/cdr/R$billName.zip") === true) {
            $xmlString = $zip->getFromName("R-$billName.xml");
            $info = self::getMapCdr($xmlString);
            $zip->close();
            return $info;
        } else {
            throw new FileException("No se encontró el archivo R$billName.zip");
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

    public static function xmlStream($billName) {
        $filePath = self::getBillPath($billName);
        if (file_exists($filePath)) {
            header('Content-Type: application/xml');
            header("Content-Disposition: attachment;filename=$billName.xml");
            header('Cache-Control:max-age=0');
            echo file_get_contents($filePath);
            exit();
        }
        throw new FileException("El archivo: $filePath no existe.");
    }

    public static function signedXmlStream($billName) {
        $filePath = self::getSignedBillPath($billName);
        if (file_exists($filePath)) {
            header('Content-Type: application/xml');
            header("Content-Disposition: attachment;filename=S-$billName.xml");
            header('Cache-Control:max-age=0');
            echo file_get_contents($filePath);
            exit();
        }
        throw new FileException("El archivo: $filePath no existe.");
    }

    public static function pdfStream($billName, $browserView = false) {
        $filePath = self::getPdfPath($billName);
        if (file_exists($filePath)) {
            header('Content-Type: application/pdf');
            if (!$browserView) {
                header("Content-Disposition: attachment;filename=$billName.pdf");
            }
            header('Cache-Control:max-age=0');
            echo file_get_contents($filePath);
            exit();
        }
        throw new FileException("El archivo: $filePath no existe.");
    }

    public static function billInputStream($billName) {
        $filePath = self::getBillInputPath($billName);
        if (file_exists($filePath)) {
            header('Content-Type: application/json');
            header("Content-Disposition: attachment;filename=$billName.json");
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
