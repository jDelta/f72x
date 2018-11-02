<?php

namespace Tests;

use F72X\Sunat\InputValidator;
use PHPUnit\Framework\TestCase;

final class InputValidatorTest extends TestCase {

    public function testValidateBase() {
        $expected = [];
        $inputValidator = new InputValidator($expected, 'BOL');
        $inputValidator->isValid();
    }
}
