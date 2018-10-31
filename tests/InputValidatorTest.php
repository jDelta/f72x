<?php

namespace Tests;

use F72X\Sunat\InputValidator;
use F72X\Sunat\Catalogo;
use PHPUnit\Framework\TestCase;

final class InputValidatorTest extends TestCase {

    public function testValidateBase() {
        $expected = [];
        $inputValidator = new InputValidator($expected, Catalogo::DOCTYPE_BOLETA);
        $inputValidator->isValid();
    }
}
