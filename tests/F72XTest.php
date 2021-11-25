<?php

namespace Tests;

use F72X\Exception\ConfigException;
use F72X\F72X;

/**
 * @testdox MÓDULO F72X
 */
final class F72XTest extends \PHPUnit_Framework_TestCase {

    /**
     * @testdox Iniciar sin definir el modo de operaración [prodMode]: No se espera errores
     */
    public function testInitWithUndefidedProdMode() {
        $config = Util::getBaseConfig();
        // remove prodMode
        unset($config['prodMode']);
        Util::initModuleWith($config);
    }

    /**
     * @testdox Iniciar con valores invalidos (null, 0, 1, -1, '0', '1', 'X', []) para el modo de operaración [prodMode]: Se espera una excepcion tipo \F72X\Exception\ConfigException
     */
    public function testInitWithWrongProdModeVarType() {
        $config = Util::getBaseConfig();
        $cases = [null, 0, 1, -1, '0', '1', 'X', []];
        // null case
        $config['prodMode'] = null;
        foreach ($cases as $idx => $case) {
            try {
                $config['prodMode'] = $case;
                Util::initModuleWith($config);
            } catch (ConfigException $exc) {
                echo "\n[$idx]... OK";
            }
        }
    }

    /**
     * @testdox Iniciar con [prodMode] = true
     */
    public function testInitWithProdModeTrue() {
        $config = Util::getBaseConfig();
        // Set prodMode
        $config['prodMode'] = true;
        Util::initModuleWith($config);
        self::assertTrue(F72X::isProductionMode());
    }

    /**
     * @testdox Iniciar con [prodMode] = false
     */
    public function testInitWithProdModeFalse() {
        $config = Util::getBaseConfig();
        // Set prodMode
        $config['prodMode'] = false;
        Util::initModuleWith($config);
        self::assertFalse(F72X::isProductionMode());
    }

}
