<?php

namespace Tests\sunat;

use F72Sunat\Sunat\Catalogo;
use PHPUnit\Framework\TestCase;

final class CatalogoTest extends TestCase {

    public function testGetCatItem() {
        $output = Catalogo::getCatItem(16, '01');
        $expected = [
            'id'    => '01',
            'value' => 'Precio unitario (incluye el IGV)'
        ];
        self::assertEquals($expected, $output);
    }

}
