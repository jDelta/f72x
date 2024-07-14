<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use F72X\Sunat\Catalogo;

final class CatalogoTest extends TestCase
{

    public function testMethodGetdocumentname()
    {
        $this->assertEquals('NOTA DE DÉBITO', Catalogo::getOfficialDocumentName(Catalogo::DOCTYPE_NOTA_DEBITO));
    }

    public function testMethodGetdocumentnameProducesAnExceptionOnInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);
        Catalogo::getOfficialDocumentName('x');
    }

    public function testGetCatItems()
    {
        $expected = [
            'NIU' => ['id' => 'NIU', 'value' => 'UNIDAD (BIENES)'],
            'ZZ' => ['id' => 'ZZ', 'value' => 'UNIDAD (SERVICIOS)']
        ];
        $actual = Catalogo::getCatItems(3);
        $this->assertEquals($expected, $actual);
    }

    public function testItemExist()
    {
        $this->assertTrue(Catalogo::itemExist(3, 'NIU'));
        $this->assertTrue(Catalogo::itemExist(3, 'ZZ'));
        $this->assertFalse(Catalogo::itemExist(3, 'XX'));
    }

    public function testGetCatItem()
    {
        $expected = [
            'id' => '01',
            'value' => 'Precio unitario (incluye el IGV)'
        ];
        $actual = Catalogo::getCatItem(16, '01');
        $this->assertEquals($expected, $actual);
    }

    public function testMethodGetDocumentShortCode()
    {
        $this->assertEquals('FAC', Catalogo::getDocumentShortCode('01'));
        $this->assertEquals('BOL', Catalogo::getDocumentShortCode('03'));
        $this->assertEquals('NCR', Catalogo::getDocumentShortCode('07'));
        $this->assertEquals('NDE', Catalogo::getDocumentShortCode('08'));

        $this->expectException(\InvalidArgumentException::class);
        Catalogo::getDocumentShortCode('09');
    }
}
