<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat;

use F72X\Sunat\Catalogo;
use F72X\Tools\Validations;

class InputValidator {

    private $data;
    private $type;
    private static $validations = [
        'operationType' => [
            'required' => true,
            'inCat' => Catalogo::CAT_FACTURA_TYPE
        ],
        'voucherSeries' => [
            'required' => true
        ],
        'voucherNumber' => [
            'required' => true
        ],
        'customerDocType' => [
            'required' => true,
            'inCat' => Catalogo::CAT_IDENT_DOCUMENT_TYPE
        ],
        'customerDocNumber' => [
            'required' => true
        ],
        'customerRegName' => [
            'required' => true
        ],
        'items' => [
            'required' => true,
            'type' => 'Array'
        ]
    ];
    private $errors = [];

    public function __construct(array $data, $type) {
        $this->data = $data;
        $this->type = $type;
        $this->validate();
    }

    public function isValid() {
        return empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }

    private function validate() {

        foreach (self::$validations as $field => $item) {
            $defauls = [
                'required' => false,
                'type' => null,
                'incat' => null
            ];
            $validation = array_merge($defauls, $item);
            if ($field == 'customerDocNumber') {
                $validation['type'] = $this->getDocTypeValidator();
            }
            $this->validateItem($field, $validation);
        }
    }

    private function validateItem($field, $validation) {
        $data = $this->data;
        $fieldExist = isset($data[$field]);
        $fieldValue = $fieldExist ? $data[$field] : null;

        $required = $validation['required'];
        $catNumber = $validation['incat'];
        $type = $validation['type'];
        // Required
        if ($required && !$fieldExist) {
            $this->errors = "$field es requerido.";
        }
        if (!$fieldExist) {
            return;
        }
        // Data type
        if ($type && !Validations::{'is' . $type}($fieldValue)) {
            $this->errors = $this->getTypeErrorValidationMessage($field, $fieldValue, $type);
        }
        // In catalog
        if ($catNumber && !Catalogo::itemExist($catNumber, $fieldValue)) {
            $this->errors = "El valor $fieldValue en el campo $field no existe en el Cátalogo N° $catNumber.";
        }
    }

    private function getTypeErrorValidationMessage($field, $value, $type) {
        switch ($type) {
            case 'Array':
                return $field == 'items' ?
                    'El campo items debe ser de tipo array.' : "Se espera que el campo $field sea un array.";
            case 'Dni':
                return "$value no es un DNI valido.";
            case 'Ruc':
                return "$value no es un DUC valido.";
            default:
                break;
        }
    }

    private function getDocTypeValidator() {
        $data = $this->data;
        $docType = isset($data['customerDocType']) ? $data['customerDocType'] : null;
        return is_null($docType) ? null : $this->getDocType($docType);
    }

    private function getDocType($docType) {
        // @IMP cases: 0, 7, A, B, C, D, E
        $cases = [
            '1' => 'Dni',
            '6' => 'Ruc'
        ];
        return isset($cases[$docType]) ? $cases[$docType] : null;
    }

}
