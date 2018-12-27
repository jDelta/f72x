<?php

namespace Tests;

use InvalidArgumentException;
use F72X\Sunat\Catalogo;
use PHPUnit\Framework\TestCase;

final class CatalogoTest extends TestCase {

    public function testMethodGetdocumentname() {
        self::assertEquals('NOTA DE DÉBITO', Catalogo::getOfficialDocumentName(Catalogo::DOCTYPE_NOTA_DEBITO));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMethodGetdocumentnameProducesAnExceptionOnInvalidType() {
        Catalogo::getOfficialDocumentName('x');
    }

    public function testGetCatItems() {
        $expected = [
            'NIU' => ['id' => 'NIU', 'value' => 'UNIDAD (BIENES)'],
            'ZZ' => ['id' => 'ZZ', 'value' => 'UNIDAD (SERVICIOS)']
        ];
        $actual = Catalogo::getCatItems(3);
        self::assertEquals($expected, $actual);
    }

    public static function testItemExist() {
        self::assertTrue(Catalogo::itemExist(3, 'NIU'));
        self::assertTrue(Catalogo::itemExist(3, 'ZZ'));
        self::assertFalse(Catalogo::itemExist(3, 'XX'));
    }

    public function testGetCatItem() {
        $expected = [
            'id' => '01',
            'value' => 'Precio unitario (incluye el IGV)'
        ];
        $actual = Catalogo::getCatItem(16, '01');
        self::assertEquals($expected, $actual);
    }

}
