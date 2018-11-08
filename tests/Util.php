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
            'address'               => 'AV. FCO. BOLOGNESI 854',
            'city'                  => 'LIMA',
            'contactInfo'           => 'Email: ventas@miweb.com',
            'usuarioSol'            => 'MODDATOS',
            'claveSol'              => 'moddatos',
            'cconfigPath'           => __DIR__.'/companyconfig',
            'repoPath'              => __DIR__.'/edocs',
            'certificate'           => '20100454523_2018_09_27',
            'prodMode'              => false
        ]);
    }
    public static function getCaseData($caseName) {
        return require "cases/$caseName.php";
    }

}
