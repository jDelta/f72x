<?php

namespace F72X\Sunat;

use ZipArchive;
use F72X\F72X;
use F72X\Company;

class ServiceGateway {

    public static function sendBill($zipName) {
//        $invoiceName = substr($zipName, 0, -3) . 'xml';
        $service_url = F72X::isProductionMode() ?
                SunatVars::SUNAT_SERVICE_URL_PROD :
                SunatVars::SUNAT_SERVICE_URL_BETA;

        $fileDir = Company::getRepositoryPath();
        $contentFile = file_get_contents("$fileDir/zip/$zipName");

        $soapService = new SunatSoapClient("$service_url?wsdl", ['trace' => true]);
        $soapService->__soapCall('sendBill', [['fileName' => $zipName, 'contentFile' => $contentFile]]);
//        try {
        $soapResponse = $soapService->__getLastResponse();
        $xml = simplexml_load_string($soapResponse);
        $response = $xml->xpath("//applicationResponse")[0];
        // CDR
        $cdr = base64_decode($response);
        file_put_contents("$fileDir/cdr/R-$zipName", $cdr);
//            $zip = new ZipArchive();
//            if ($zip->open("$fileDir/cdr/$zipName") === TRUE) {
////                $cdrXml = $zip->getFromName("R-$invoiceName");
////                file_put_contents("$fileDir/cdr/R-$invoiceName", $cdrXml);
//                $zip->close();
//            }
//        } catch (Exception $exc) {
//            throw new Exception();
//        }
    }

}
