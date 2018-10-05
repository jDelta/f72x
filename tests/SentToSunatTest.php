<?php

namespace Tests;

use F72X\Sunat\ServiceGateway;

use PHPUnit\Framework\TestCase;

final class SentToSunatTest extends TestCase {
    public function __construct() {
        Util::initF72X();
    }
    public static function testSendBoletaCase1() {
        ServiceGateway::sendBill('20100454523-03-B001-00003652.zip');
    }

    public static function testSendFacturaCase1() {
        ServiceGateway::sendBill('20100454523-01-F001-00004355.zip');
    }

}
