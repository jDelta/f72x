<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Object;

class PostalAddress {

    private $id;
    private $streetName;
    private $citySubdivisionName;
    private $cityName;
    private $countrySubentity;
    private $district;
    private $countryCode = 'PE';

    public function getId() {
        return $this->id;
    }

    public function getStreetName() {
        return $this->streetName;
    }

    public function getCitySubdivisionName() {
        return $this->citySubdivisionName;
    }

    public function getCityName() {
        return $this->cityName;
    }

    public function getCountrySubentity() {
        return $this->countrySubentity;
    }

    public function getDistrict() {
        return $this->district;
    }

    public function getCountryCode() {
        return $this->countryCode;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setStreetName($streetName) {
        $this->streetName = $streetName;
        return $this;
    }

    public function setCitySubdivisionName($citySubdivisionName) {
        $this->citySubdivisionName = $citySubdivisionName;
        return $this;
    }

    public function setCityName($cityName) {
        $this->cityName = $cityName;
        return $this;
    }

    public function setCountrySubentity($countrySubentity) {
        $this->countrySubentity = $countrySubentity;
        return $this;
    }

    public function setDistrict($district) {
        $this->district = $district;
        return $this;
    }

    public function setCountryCode($countryCode) {
        $this->countryCode = $countryCode;
        return $this;
    }

}
