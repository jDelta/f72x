<?php

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
     * @return string
     */
    private static function get($key) {
        $value = null;
        if (empty(self::$_CONFIG)) {
            throw new ConfigException();
        }
        if (isset(self::$_CONFIG[$key])) {
            $value = self::$_CONFIG[$key];
        }
        if (is_null($value)) {
            throw new ConfigException(sprintf('La propiedad %s no puede ser null, por favor revise su cofiguración', $key));
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
        return self::get('RUC');
    }

    /**
     * 
     * @return string
     */
    public static function getCompanyName() {
        return self::get('RAZON_SOCIAL');
    }

    /**
     * 
     * @return string
     */
    public static function getBusinessName() {
        return self::get('NOMBRE_COMERCIAL');
    }

    /**
     * 
     * @return string
     */
    public static function getRegAddressCode() {
        return self::get('CODIGO_DOMICILIO_FISCAL');
    }

    /**
     * 
     * @return string
     */
    public static function getSolUser() {
        return self::get('USUARIO_SOL');
    }

    /**
     * 
     * @return string
     */
    public static function getSolKey() {
        return self::get('CLAVE_SOL');
    }

    /**
     * 
     * @return string
     */
    public static function getCertPath() {
        return self::get('RUTA_CERTIFICADO');
    }

    /**
     * 
     * @return string
     */
    public static function getRepositoryPath() {
        return self::get('RUTA_REPOSITORIO');
    }

}
