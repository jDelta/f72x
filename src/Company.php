<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X;

use F72X\Exception\ConfigException;

class Company {

    /** @var array cache */
    private static $_CONFIG = [];

    /**
     * 
     * Get Configuration Value
     * 
     * @param string $key
     * @param boolean $nullExc
     * @return string
     */
    public static function get($key, $nullExc = true) {
        $value = null;
        if (empty(self::$_CONFIG)) {
            throw new ConfigException('Olvidaste configurar el Modulo F72X usa \F72X\F72::init($config)');
        }
        if (isset(self::$_CONFIG[$key])) {
            $value = self::$_CONFIG[$key];
        }
        if (is_null($value) && $nullExc) {
            throw new ConfigException(sprintf('La propiedad %s no ha sido definida, por favor revise su cofiguración', $key));
        }
        return $value;
    }

    /**
     * 
     * @param array $config
     */
    public static function setConfig($config) {
        self::$_CONFIG = $config;
    }

    /**
     * 
     * @return string
     */
    public static function getRUC() {
        return self::get('ruc');
    }

    /**
     * 
     * @return string
     */
    public static function getCompanyName() {
        return self::get('razonSocial');
    }

    /**
     * 
     * @return string
     */
    public static function getBusinessName() {
        return self::get('nombreComercial', false);
    }

    /**
     * 
     * @return string
     */
    public static function getRegAddressCode() {
        return self::get('codigoDomicilioFiscal');
    }

    /**
     * 
     * @return string
     */
    public static function getAddress() {
        return self::get('address');
    }

    /**
     * 
     * @return string
     */
    public static function getCity() {
        return self::get('city');
    }
    /**
     * 
     * @return string
     */
    public static function getContactInfo() {
        return self::get('contactInfo');
    }
    /**
     * 
     * @return string
     */
    public static function getSolUser() {
        return self::get('usuarioSol');
    }

    /**
     * 
     * @return string
     */
    public static function getSolKey() {
        return self::get('claveSol');
    }

    /**
     * 
     * @return string
     */
    public static function getCertPath() {
        return self::get('cconfigPath') . '/certs/' . self::get('certificate') . '.pem';
    }

    /**
     * 
     * @return string
     */
    public static function getRepositoryPath() {
        return self::get('repoPath');
    }

    /**
     * 
     * @return string
     */
    public static function getListsPath() {
        return self::get('cconfigPath') . '/lists';
    }

    /**
     * 
     * @return string
     */
    public static function getPdfTemplatesPath() {
        return self::get('cconfigPath') . '/tpls';
    }

}
