<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Codelint\QRCode\QRCode;

final class QrTest extends TestCase
{

    public function testGen()
    {
        $this->expectNotToPerformAssertions();
        $qr = new QRCode();
        $qr->png('Hi! Developer!', __DIR__ . '/temp/qr.png', 'Q', 8, 2);
    }
}
