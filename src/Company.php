<?php

namespace F72X;

use F72X\Exception\ConfigException;

class Company {

    /** @var array cache */
    private static $_CONFIG;

    /**
     * 
     * Get Configuration Value
     * 
     * @param string $key
     * @return mixed
     */
    private static function get($key) {
        $value = null;
        if (!self::$_CONFIG) {
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

    public static function setConfig($config) {
        self::$_CONFIG = $config;
    }

    public static function getRUC() {
        return self::get('RUC');
    }

    public static function getCompanyName() {
        return self::get('RAZON_SOCIAL');
    }

    public static function getBusinessName() {
        return self::get('NOMBRE_COMERCIAL');
    }

    public static function getRegAddressCode() {
        return self::get('CODIGO_DOMICILIO_FISCAL');
    }

    public static function getSolUser() {
        return self::get('USUARIO_SOL');
    }

    public static function getSolKey() {
        return self::get('CLAVE_SOL');
    }

    public static function getCertPath() {
        return self::get('RUTA_CERTIFICADO');
    }

    public static function getRepositoryPath() {
        return self::get('RUTA_REPOSITORIO');
    }

}
