<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat\Document;

use F72X\Sunat\InputValidator;

class ComunicacionBaja extends AbstractSummary {

    protected $idPrefix = 'RA';
    protected $xmlTplName = 'VoidedDocuments.xml';
    protected $validations = [
        'legalValidity' => ['required' => true]
    ];

    public function validateInput(array $inputData) {
        $validator = new InputValidator($inputData, $this->validations);
        if (!$validator->isValid()) {
            $msg = $validator->getErrosString();
            throw new \Exception("Invalid Document Input: [$msg]");
        }
    }

}
