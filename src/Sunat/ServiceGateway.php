<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat;

use Exception;
use F72X\Repository;
use F72X\Exception\SunatException;
class ServiceGateway {

    /**
     * 
     * @param string $billName El nombre del documento electrónico.
     * @return array
     */
    public static function sendBill($billName) {
        $contentFile = Repository::getZippedBillContent($billName);
        try {
            $soapService = SunatSoapClient::getService();
            $soapService->__soapCall('sendBill', [['fileName' => "$billName.zip", 'contentFile' => $contentFile]]);
            $serverResponse = $soapService->__getLastResponse();
        } catch (Exception $exc) {
            throw new SunatException($exc->getMessage(), $exc->getCode());
        }

        // Save Constancia de recepción
        self::saveCdr($serverResponse, $billName);
        // Get Response info
        return Repository::getCdrInfo($billName);
    }

    private static function saveCdr($response, $billName) {
        $xml = simplexml_load_string($response);
        $appResp = $xml->xpath("//applicationResponse")[0];
        // CDR
        $cdr = base64_decode($appResp);
        Repository::saveCdr($billName, $cdr);
    }

}
