<?php

namespace Tests;

use F72X\Sunat\InputValidator;

final class InputValidatorTest extends \PHPUnit_Framework_TestCase {

    public function testValidateBase() {
        $expected = [];
        $inputValidator = new InputValidator($expected, 'BOL');
        $inputValidator->isValid();
    }
}
