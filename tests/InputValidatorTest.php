<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use F72X\Sunat\InputValidator;

final class InputValidatorTest extends TestCase
{

    public function testValidateBase()
    {
        $this->expectNotToPerformAssertions();
        $expected = [];
        $inputValidator = new InputValidator($expected, 'BOL');
        $inputValidator->isValid();
    }
}
