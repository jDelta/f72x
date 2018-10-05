<?php

namespace Tests;

use F72X\Sunat\Catalogo;
use PHPUnit\Framework\TestCase;

final class CatalogoTest extends TestCase {

    public function testGetCatItem() {
        $output = Catalogo::getCatItem(16, '01');
        $expected = [
            'id' => '01',
            'value' => 'Precio unitario (incluye el IGV)'
        ];
        self::assertEquals($expected, $output);
    }

    public static function getCaseData($caseName) {
        return require "cases/$caseName.php";
    }

}
