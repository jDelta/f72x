<?php

namespace Tests;

use F72X\F72X;

final class Util {

    public static function initF72X() {
        F72X::init([
            'ruc'                   => '20100454523',
            'razonSocial'           => 'Soporte Tecnológicos EIRL',
            'nombreComercial'       => 'Tu Soporte',
            'codigoDomicilioFiscal' => '0000',
            'usuarioSol'            => 'MODDATOS',
            'claveSol'              => 'moddatos',
            'cconfigPath'           => __DIR__.'/companyconfig',
            'repoPath'              => __DIR__.'/edocs',
            'certName'              => '20100454523_2018_09_27.pem',
            'prodMode'              => false
        ]);
    }
    public static function getCaseData($caseName) {
        return require "cases/$caseName.php";
    }

}
