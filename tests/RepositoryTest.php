<?php

namespace Tests;

use F72X\Repository;

final class RepositoryTest extends \PHPUnit_Framework_TestCase {

    protected function setUp() {
        Util::initModule();
    }

    public function testGdrInfo() {
//        Repository::getCdrInfo('20100454523-01-F001-00004355');
    }

    public function removeBillDocs() {
        Repository::removeFiles('20100454523-01-F001-00004355');
        Repository::removeFiles('20100454523-03-B001-00004355');
    }
}
