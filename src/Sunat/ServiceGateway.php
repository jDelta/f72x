<?php

namespace F72X\Sunat;

use ZipArchive;
use F72X\F72X;
use F72X\Company;
use F72X\Repository;
use F72X\Tools\FileService;

class ServiceGateway {

    /**
     * 
     * @param type $billName El nombre del documento electrónico.
     */
    public static function sendBill($billName) {
//        $invoiceName = substr($zipName, 0, -3) . 'xml';
        $repository = Company::getRepositoryPath();
        $contentFile = file_get_contents("$repository/zippedbill/$billName.zip");

        $soapService = SunatSoapClient::getService();
        $soapService->__soapCall('sendBill', [['fileName' => "$billName.zip", 'contentFile' => $contentFile]]);
        try {
            $serverResponse = $soapService->__getLastResponse();
            // Save Constancia de recepción
            self::saveCdr($serverResponse, $billName);
            // Get Response info
            return FileService::getCdrInfo($billName);
        } catch (Exception $exc) {
            throw new Exception();
        }
    }

    private static function saveCdr($response, $billName) {
        $xml = simplexml_load_string($response);
        $appResp = $xml->xpath("//applicationResponse")[0];
        // CDR
        $cdr = base64_decode($appResp);
        Repository::saveCdr($billName, $cdr);
    }

}
