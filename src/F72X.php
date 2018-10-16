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

class F72X {

    private static $production = false;
    private static $requiredConfigFields = [
        'ruc',
        'razonSocial',
        'nombreComercial',
        'codigoDomicilioFiscal',
        'usuarioSol',
        'claveSol',
        'cconfigPath',
        'repoPath',
        'certificate',
        'prodMode'
    ];

    /**
     * Inicializa el módulo con la configuración del contribuyente.
     * @param array $cfg datos del contribuyente
     * @throws ConfigException
     */
    public static function init(array $cfg) {
        self::validateConfig($cfg);
        self::$production = !!$cfg['prodMode'];
        Company::setConfig($cfg);
    }

    public static function isProductionMode() {
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
        return __DIR__.'/..';
    }
    public static function getTempDir() {
        return self::getModuleDir().'/temp';
    }
}
