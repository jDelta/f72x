<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 *
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat;

use Exception;
use F72X\Repository;
use F72X\Exception\SunatException;

class ServiceGateway
{
    private $soapService;
    public function __construct()
    {
        $this->soapService = SunatSoapClient::getService();
    }

    /**
     *
     * @param string $documentName El nombre del documento electrónico.
     * @return array
     */
    public function sendBill($documentName)
    {
        $contentFile = Repository::getZipContent($documentName);
        try {
            $this->soapService->__soapCall('sendBill', [['fileName' => "$documentName.zip", 'contentFile' => $contentFile]]);
            $serverResponse = $this->soapService->__getLastResponse();
        } catch (Exception $exc) {
            throw new SunatException($exc->getMessage(), $exc->getCode());
        }

        // Save Constancia de recepción
        $this->saveCdr($serverResponse, $documentName);
        // Get Response info
        return Repository::getCdrInfo($documentName);
    }

    /**
     *
     * @param string $documentName El nombre del documento electrónico.
     * @return string El ticket de recepción.
     */
    public function sendSummary($documentName)
    {
        $contentFile = Repository::getZipContent($documentName);
        try {
            $this->soapService->__soapCall('sendSummary', [['fileName' => "$documentName.zip", 'contentFile' => $contentFile]]);
            $serverResponse = $this->soapService->__getLastResponse();
        } catch (Exception $exc) {
            throw new SunatException($exc->getMessage(), $exc->getCode());
        }

        // Save and return ticket
        return $this->saveTicket($serverResponse, $documentName);
    }

    public function getStatus($documentName)
    {
        $ticket = Repository::getTicketInfo($documentName);
        try {
            $this->soapService->__soapCall('getStatus', [['ticket' => $ticket]]);
            $serverResponse = $this->soapService->__getLastResponse();
        } catch (Exception $exc) {
            throw new SunatException($exc->getMessage(), $exc->getCode());
        }
        // Save Constancia de recepción
        return $this->saveStatusResponse($serverResponse, $documentName);
    }

    private function saveCdr($response, $documentName)
    {
        $xml = simplexml_load_string($response);
        $appResp = $xml->xpath("//applicationResponse")[0];
        // CDR
        $cdr = base64_decode($appResp);
        Repository::saveCdr($documentName, $cdr);
    }

    private function saveStatusResponse($response, $documentName)
    {
        $xml = simplexml_load_string($response);
        $status = (array)$xml->xpath("//status")[0];

        // Status code only 0 and 99
        if ($status['statusCode'] == '0' || $status['statusCode'] == '99') {
            $statusContent = $status['content'];
            $cdr = base64_decode($statusContent);
            Repository::saveCdr($documentName, $cdr);
            $status['cdr'] = Repository::getCdrInfo($documentName);
            $status['message'] = null;
        } else {
            $status['cdr'] = null;
            $status['message'] = $status['content'];
        }
        unset($status['content']);
        return $status;
    }

    private function saveTicket($response, $documentName)
    {
        $xmlObj = simplexml_load_string($response);
        // Ticket
        $ticket = (string) $xmlObj->xpath("//ticket")[0];
        Repository::saveTicket($documentName, $ticket);
        return $ticket;
    }
}
