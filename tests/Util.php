<?php

namespace Tests;

use F72X\F72X;

final class Util {

    public static function initF72X() {
        F72X::init([
            'RUC'                     => '20100454523',
            'RAZON_SOCIAL'            => 'Soporte Tecnológicos EIRL',
            'NOMBRE_COMERCIAL'        => 'Tu Soporte',
            'USUARIO_SOL'             => 'MODDATOS',
            'CLAVE_SOL'               => 'moddatos',
            'CODIGO_DOMICILIO_FISCAL' => '0000',
            'RUTA_CERTIFICADO'        => __DIR__.'/cert/20100454523_cert.pem',
            'RUTA_REPOSITORIO'        => __DIR__.'/repository'
        ]);
    }
    public static function getCaseData($caseName) {
        return require "cases/$caseName.php";
    }

}
