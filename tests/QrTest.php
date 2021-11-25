<?php

namespace Tests;

use Codelint\QRCode\QRCode;

final class QrTest extends \PHPUnit_Framework_TestCase {

    protected function setUp() {
        Util::initModule();
    }

    public function testGen() {
        $qr = new QRCode();
        $qr->png('Hi! Developer!', __DIR__ . '/temp/qr.png', 'Q', 8, 2);
    }

}
