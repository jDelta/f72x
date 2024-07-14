<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use F72X\Repository;

final class RepositoryTest extends TestCase
{

    public function testGdrInfo()
    {
        $this->expectNotToPerformAssertions();
        //        Repository::getCdrInfo('20100454523-01-F001-00004355');
    }

    public function removeBillDocs()
    {
        Repository::removeFiles('20100454523-01-F001-00004355');
        Repository::removeFiles('20100454523-03-B001-00004355');
    }
}
