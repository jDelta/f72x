<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use F72X\Exception\ConfigException;
use F72X\F72X;

/**
 * @testdox MÓDULO F72X
 */
final class F72XTest extends TestCase
{

    /**
     * @testdox Iniciar sin definir el modo de operaración [prodMode]: No se espera errores
     */
    public function testInitWithUndefidedProdMode()
    {
        $this->expectNotToPerformAssertions();
        $config = Util::getBaseConfig();
        // remove prodMode
        unset($config['prodMode']);
        Util::initModuleWith($config);
    }

    public static function wrongProdModeVarParamTypeProvider(): array
    {
        return [
            [null, ConfigException::class, 'null'],
            [0,    ConfigException::class, 'null'],
            [1,    ConfigException::class, 'null'],
            [-1,   ConfigException::class, 'null'],
            ['0',  ConfigException::class, 'null'],
            ['1',  ConfigException::class, 'null'],
            ['X',  ConfigException::class, 'null'],
            [[],   ConfigException::class, 'null'],
        ];
    }
    /**
     * @testdox Iniciar con valores invalidos (null, 0, 1, -1, '0', '1', 'X', []) para el modo de operaración [prodMode]: Se espera una excepcion tipo \F72X\Exception\ConfigException
     * @dataProvider wrongProdModeVarParamTypeProvider
     */
    public function testInitWithWrongProdModeVarType2($input, $expectedExceptionType, $description)
    {
        $config = Util::getBaseConfig();
        $config['prodMode'] = $input;
        $this->expectException($expectedExceptionType);
        Util::initModuleWith($config);
    }
    /**
     * @testdox Iniciar con [prodMode] = true
     */
    public function testInitWithProdModeTrue()
    {
        $config = Util::getBaseConfig();
        // Set prodMode
        $config['prodMode'] = true;
        Util::initModuleWith($config);
        self::assertTrue(F72X::isProductionMode());
    }

    /**
     * @testdox Iniciar con [prodMode] = false
     */
    public function testInitWithProdModeFalse()
    {
        $config = Util::getBaseConfig();
        // Set prodMode
        $config['prodMode'] = false;
        Util::initModuleWith($config);
        self::assertFalse(F72X::isProductionMode());
    }
}
