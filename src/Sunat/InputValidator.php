<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat;

use F72X\F72X;
use F72X\Sunat\Catalogo;
use F72X\Tools\Validations;

class InputValidator {

    private $data;
    private $type;
    private $errors = [];
    private $validations = null;

    public function __construct(array $data, $type) {
        $this->data = $data;
        $this->type = $type;
        if(is_array($type)){
            $this->validations = $type;
        }
        $this->validate();
    }

    public function isValid() {
        return empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }

    private function validate() {
        if(!$this->validations){
            $this->validations = $this->getValidations();
        }
        foreach ($this->validations as $field => $item) {
            $defauls = [
                'required' => false,
                'type' => null,
                'incat' => null
            ];
            $validation = array_merge($defauls, $item);
            $this->validateItem($field, $validation);
        }
    }

    private function getValidations() {
        $validationFile = F72X::getSrcDir() . '/validations/' . $this->type . '.php';
        return require $validationFile;
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
            $this->errors[$field][] = "Requerido";
        }
        if (!$fieldExist) {
            return;
        }
        // Data type
        if ($type && !Validations::{'is' . $type}($fieldValue)) {
            $this->errors[$field][] = $this->getTypeErrorValidationMessage($field, $fieldValue, $type);
        }
        // In catalog
        if ($catNumber && !Catalogo::itemExist($catNumber, $fieldValue)) {
            $this->errors[$field][] = "El valor $fieldValue no existe en el Cátalogo N° $catNumber.";
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

    public function throwExc() {
        throw new \F72X\Exception\InvalidInputException($this->getErrosString(), $this->getErrors());
    }

    public function getErrosString() {
        $errs = [];
        foreach ($this->errors as $field => $fErrors) {
            $errs[] = $field . ': (' . implode(',', $fErrors) . ')';
        }
        return implode(',', $errs);
    }

}
