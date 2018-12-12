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

    public function testGeneratePhpArrays() {
        $path = __DIR__ . '/../src/Sunat/catalogo';
        $cats = Catalogo::getAllCatNumbers();
        foreach ($cats as $num) {
            $num = str_pad($num, 2, '0', STR_PAD_LEFT);
            Catalogo::catItemsToPhpArray($num, "$path/CAT_$num.php");
        }
    }

    public function testGenerateJsFile() {
        $requiredCats = Catalogo::getAllCatNumbers();
        Catalogo::catsToJsFile($requiredCats, __DIR__ . '/cats.js');
    }

}
