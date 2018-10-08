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
        'certPath',
        'repoPath',
        'prodMode'
    ];

    /**
     * 
     * @param array $cfg
     */
    public static function init(array $cfg) {
        self::validateConfig($cfg);
        self::$production = !!$cfg['prodMode'];
        Company::setConfig($cfg);
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
