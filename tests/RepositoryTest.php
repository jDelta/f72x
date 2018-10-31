<?php

namespace Tests;

use F72X\Repository;
use PHPUnit\Framework\TestCase;

final class RepositoryTest extends TestCase {

    protected function setUp() {
        Util::initF72X();
    }

    public function testGdrInfo() {
//        Repository::getCdrInfo('20100454523-01-F001-00004355');
    }

    public function removeBillDocs() {
        Repository::removeBillDocs('20100454523-01-F001-00004355');
        Repository::removeBillDocs('20100454523-03-B001-00004355');
    }
}
