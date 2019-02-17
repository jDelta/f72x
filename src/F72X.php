<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X;

use F72X\Exception\ConfigException;

class F72X {

    private static $production = null;
    private static $requiredConfigFields = [
        'cconfigPath',
        'repoPath'
    ];

    /**
     * Inicializa el módulo con la configuración del contribuyente.
     * @param array $cfg datos del contribuyente
     * @throws ConfigException
     */
    public static function init(array $cfg) {
        self::validateConfig($cfg);
        // Wrong production mode config
        if (array_key_exists('prodMode', $cfg) && !is_bool($cfg['prodMode'])) {
            $vartype = gettype($cfg['prodMode']);
            throw new ConfigException("F72x::init error: La propiedad [prodMode] debe ser tipo boolean, no $vartype. O puede no indicar esta propiedad si no piensa hacer uso de los webservices de SUNAT");
        }
        if (isset($cfg['prodMode'])) {
            self::$production = $cfg['prodMode'];
        }
        Company::setConfig($cfg);
    }

    public static function isProductionMode() {
        if (is_null(self::$production)) {
            throw new ConfigException("F72x::init error: La propiedad [prodMode] no fue definida");
        }
        return self::$production;
    }

    /**
     * 
     * @param array $config
     * @throws ConfigException
     */
    private static function validateConfig(array $config) {
        foreach (self::$requiredConfigFields as $field) {
            if (!isset($config[$field])) {
                throw new ConfigException(sprintf('La propiedad %s es obligatoria, por favor revise su cofiguración.', $field));
            }
        }
        if (!file_exists($config['cconfigPath'])) {
            throw new ConfigException(sprintf('No se encuentra el directorio de configuración del contribuyente, verifique la ubicación %s sea la correcta.', $config['cconfigPath']));
        }
        if (!file_exists($config['repoPath'])) {
            throw new ConfigException(sprintf('No se encuentra el directorio que será usado para guardar los documentos electronicos, verifique la ubicación %s sea la correcta.', $config['repoPath']));
        }
    }

    public static function getModuleDir() {
        return __DIR__ . '/..';
    }

    public static function getTempDir() {
        return self::getModuleDir() . '/temp';
    }

    public static function getDefaultPdfTemplatesPath() {
        return self::getModuleDir() . '/cdefaults/tpls';
    }

    public static function getDefaultListsPath() {
        return self::getModuleDir() . '/cdefaults/lists';
    }

    public static function getSrcDir() {
        return __DIR__;
    }

}
