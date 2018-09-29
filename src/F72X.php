<?php

namespace F72X;

use F72X\Exception\ConfigException;

class F72X {

    private static $production;
    private static $requiredConfigFields = [
        'RUC',
        'RAZON_SOCIAL',
        'NOMBRE_COMERCIAL',
        'USUARIO_SOL',
        'CLAVE_SOL',
        'CODIGO_DOMICILIO_FISCAL',
        'RUTA_CERTIFICADO',
        'RUTA_REPOSITORIO'
    ];

    /**
     * 
     * @param boolean $config
     * @param type $prodMode
     */
    public static function init($config, $prodMode = false) {
        self::$production = $prodMode;
        self::validateConfig($config);
        Company::setConfig($config);
    }

    public static function isProductionMode() {
        return self::$production;
    }

    private static function validateConfig($config) {
        foreach (self::$requiredConfigFields as $field) {
            if (!isset($config[$field])) {
                throw new ConfigException(sprintf('La propiedad %s es obligatoria, por favor revise su cofiguración!', $field));
            }
        }
    }

}
