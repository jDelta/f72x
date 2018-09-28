<?php

namespace Tests\Sunat;

use F72X\Sunat\ServiceGateway;
use PHPUnit\Framework\TestCase;

final class ServiceGatewayTest extends TestCase {

    public static function testSendBill() {
        ServiceGateway::sendBill('20393948125-01-F001-00004355.zip');
    }

}
