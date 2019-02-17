<?php

namespace Tests;

use F72X\F72X;

final class Util {

    public static $defaultDemoConfig = [
        'ruc'                   => '20100454523',
        'razonSocial'           => 'Soporte Tecnológicos EIRL',
        'nombreComercial'       => 'Tu Soporte',
        'codigoDomicilioFiscal' => '0000',
        'address'               => 'AV. FCO. BOLOGNESI 854',
        'city'                  => 'LIMA',
        'contactInfo'           => 'Email: ventas@miweb.com',
        'usuarioSol'            => 'MODDATOS',
        'claveSol'              => 'moddatos',
        'cconfigPath'           => __DIR__.'/companyconfig',
        'repoPath'              => __DIR__.'/edocs',
        'prodMode'              => false
    ];

    public static function initModule() {
        F72X::init(self::$defaultDemoConfig);
    }

    public static function initModuleWith(array $config) {
        F72X::init($config);
    }

    public static function getBaseConfig() {
        return self::$defaultDemoConfig;
    }

    public static function getCaseData($caseName) {
        return require "cases/$caseName.php";
    }

}
