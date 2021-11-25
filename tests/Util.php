<?php

namespace Tests;

use F72X\F72X;

final class Util {
    public static function getDefaultDemoConfig() {
        return [
            'ruc'                   => '20100454523',
            'razonSocial'           => 'Soporte Tecnológicos EIRL',
            'nombreComercial'       => 'Tu Soporte',
            'codigoDomicilioFiscal' => '0000',
            'address'               => 'AV. FCO. BOLOGNESI 854',
            'city'                  => 'LIMA',
            'edocHeaderContent'     => '<i>Email: ventas@miweb.com</i>',
            'edocFooterContent'     => '<small>Lo que hacemos, lo hacemos bien!</small>',
            'usuarioSol'            => 'MODDATOS',
            'claveSol'              => 'moddatos',
            'cconfigPath'           => __DIR__.'/companyconfig',
            'repoPath'              => __DIR__.'/edocs',
            'tempPath'              => __DIR__.'/temp',
            'certificate'           => 'activecert',
            'prodMode'              => false
        ];
    }

    public static function initModule() {
        F72X::init(self::getDefaultDemoConfig());
    }

    public static function initModuleWith(array $config) {
        F72X::init($config);
    }

    public static function getBaseConfig() {
        return self::getDefaultDemoConfig();
    }

    public static function getCaseData($caseName) {
        return require "cases/$caseName.php";
    }

}
