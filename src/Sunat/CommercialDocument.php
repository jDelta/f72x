<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat;

use DateTime;
use F72X\Sunat\Operations;
use F72X\Company;

class CommercialDocument {

    private $currencyCode;
    private $documentType;
    private $documentNumber;
    private $documentId;
    private $documentFileName;
    private $issueDate;
    private $referenceDate;
    private $companyDocType;
    private $companyRUC;
    private $companyName;
    private $items;
    private $rawData;
    private $parsedData;

    public function __construct($type, $rawData) {
        $parsedData = $this->parseData($rawData);
        $this->currencyCode = $parsedData['currencyCode'];
        $this->documentType = $type;
        $this->documentNumber = str_pad($rawData['documentNumber'], 5, '0', STR_PAD_LEFT);
        $this->issueDate = new DateTime($parsedData['issueDate']);
        $this->referenceDate = new DateTime($parsedData['referenceDate']);
        $this->documentId = $this->documentType.'-' . $this->issueDate->format('Ymd') . '-' . $this->documentNumber;
        $this->documentFileName = Company::getRUC() . '-' . $this->documentId;
        $this->rawData = $rawData;
        $this->parsedData = $parsedData;
        $this->companyDocType = '6'; // RUC
        $this->companyRUC = Company::getRUC();
        $this->companyName = Company::getCompanyName();
        $this->items = $parsedData['items'];
    }
    public function getCurrencyCode() {
        return $this->currencyCode;
    }

    public function getDocumentType() {
        return $this->documentType;
    }

    public function getDocumentNumber() {
        return $this->documentNumber;
    }

    public function getDocumentId() {
        return $this->documentId;
    }

    public function getDocumentFileName() {
        return $this->documentFileName;
    }

    public function getIssueDate() {
        return $this->issueDate;
    }

    public function getReferenceDate() {
        return $this->referenceDate;
    }

    public function getCompanyRUC() {
        return $this->companyRUC;
    }

    public function getCompanyName() {
        return $this->companyName;
    }

    public function getItems() {
        return $this->items;
    }

    public function getRawData() {
        return $this->rawData;
    }

    public function getParsedData() {
        return $this->parsedData;
    }

    public function setCurrencyCode($currencyCode) {
        $this->currencyCode = $currencyCode;
        return $this;
    }

    public function setDocumentType($documentType) {
        $this->documentType = $documentType;
        return $this;
    }

    public function setDocumentNumber($documentNumber) {
        $this->documentNumber = $documentNumber;
        return $this;
    }

    public function setDocumentId($documentId) {
        $this->documentId = $documentId;
        return $this;
    }

    public function setIssueDate($issueDate) {
        $this->issueDate = $issueDate;
        return $this;
    }

    public function setReferenceDate($referenceDate) {
        $this->referenceDate = $referenceDate;
        return $this;
    }

    public function setCompanyRUC($companyRUC) {
        $this->companyRUC = $companyRUC;
        return $this;
    }

    public function setCompanyName($companyName) {
        $this->companyName = $companyName;
        return $this;
    }
    
    public function getDataForXml() {
        return [
            'documentId'    => $this->documentId,
            'currencyCode'  => $this->currencyCode,
            'issueDate'     => $this->issueDate->format('Y-m-d'),
            'referenceDate' => $this->referenceDate->format('Y-m-d'),
            'companyRUC'    => $this->companyRUC,
            'companyName'   => $this->companyName,
            'items'         => $this->items
        ];
    }

    private function parseData($rawData) {
        $headData = [
            'companyDocType' => 6, // RUC
            'companyRUC'     => Company::getRUC(),
            'companyName'    => Company::getCompanyName()
        ];
        $parsedData = array_merge($rawData, $headData);
        $itemDefaults = [
            'taxableOperations' => 0,
            'exemptedOperations' => 0,
            'unaffectedOperations' => 0,
            'freeOperations' => 0,
            'totalCharges' => 0,
            'totalIsc' => 0,
            'totalIgv' => 0,
            'totalOtherTaxes' => 0,
            'affectedDocType' => null,
            'affectedDocSeries' => null,
            'affectedDocNumber' => null,
            'perceptionRegimeType' => null,
            'perceptionPercentage' => null,
            'perceptionBaseAmount' => null,
            'perceptionAmount' => null,
            'perceptionIncludedAmount' => null
        ];
        $parsedItems = [];
        foreach ($rawData['items'] as $item) {
            $item = array_merge($itemDefaults, $item);
            $item2 = [
                'payableAmount'        => Operations::formatAmount($item['payableAmount']),
                'taxableOperations'    => Operations::formatAmount($item['taxableOperations']),
                'exemptedOperations'   => Operations::formatAmount($item['exemptedOperations']),
                'unaffectedOperations' => Operations::formatAmount($item['unaffectedOperations']),
                'freeOperations'       => Operations::formatAmount($item['freeOperations']),
                'totalCharges'         => Operations::formatAmount($item['totalCharges']),
                'totalIsc'             => Operations::formatAmount($item['totalIsc']),
                'totalIgv'             => Operations::formatAmount($item['totalIgv']),
                'totalOtherTaxes'      => Operations::formatAmount($item['totalOtherTaxes']),
                'perceptionPercentage' => Operations::formatAmount($item['perceptionPercentage']),
                'perceptionBaseAmount' => Operations::formatAmount($item['perceptionBaseAmount']),
                'perceptionAmount'     => Operations::formatAmount($item['perceptionAmount']),
                'perceptionIncludedAmount'  => Operations::formatAmount($item['perceptionIncludedAmount']),
            ];
            $parsedItems[] = array_merge($item, $item2);
        }
        $parsedData['items'] = $parsedItems;
        return $parsedData;
    }

}
