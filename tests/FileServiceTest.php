<?php

namespace Tests;

use F72X\Tools\FileService;
use PHPUnit\Framework\TestCase;

final class FileServiceTest extends TestCase {

    protected function setUp() {
        Util::initF72X();
    }

    public function testGdrInfo() {
        FileService::getCdrInfo('20100454523-01-F001-00004355');
    }

}
