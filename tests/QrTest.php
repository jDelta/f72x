<?php

namespace Tests;

use Codelint\QRCode\QRCode;
use PHPUnit\Framework\TestCase;

final class QrTest extends TestCase {

    protected function setUp() {
        Util::initF72X();
    }

    public function testGen() {
        $qr = new QRCode();
        $qr->png('Hi! Developer!', 'temp/qr.png', 'Q', 8, 2);
    }

}
