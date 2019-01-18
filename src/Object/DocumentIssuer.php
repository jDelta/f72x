<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Object;

/**
 * DocumentIssuer
 * 
 * This represents the entity that generates the document
 */
class DocumentIssuer {

    /**
     * The company's identification document type
     * 
     * Default: 6 = RUC
     * 
     * @var string 
     */
    private $idDocType = '6';

    /**
     * The company's identification document number
     * 
     * Default: RUC Number
     * 
     * @var string 
     */
    private $idDocNumber;

    /**
     * The company's registration name
     * 
     * Default: Razón Social
     * 
     * @var string 
     */
    private $regName;

    /**
     * The company's commercial name
     * 
     * Default: Nombre comercial
     * 
     * @var string 
     */
    private $comName;

    /**
     * The company's postal address.
     * @var PostalAddress
     */
    private $postalAddress;

    public function getIdDocType() {
        return $this->idDocType;
    }

    public function getIdDocNumber() {
        return $this->idDocNumber;
    }

    public function getRegName() {
        return $this->regName;
    }

    public function getComName() {
        return $this->comName;
    }

    public function getPostalAddress() {
        return $this->postalAddress;
    }

    public function setIdDocType($idDocType) {
        $this->idDocType = $idDocType;
        return $this;
    }

    public function setIdDocNumber($idDocNumber) {
        $this->idDocNumber = $idDocNumber;
        return $this;
    }

    public function setRegName($regName) {
        $this->regName = $regName;
        return $this;
    }

    public function setComName($comName) {
        $this->comName = $comName;
        return $this;
    }

    public function setPostalAddress(PostalAddress $postalAddress) {
        $this->postalAddress = $postalAddress;
        return $this;
    }

}
