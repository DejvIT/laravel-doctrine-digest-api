<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Concerns\AuthenticatesBloggers;
use Tests\Concerns\RefreshDoctrineDatabase;
use Tests\Concerns\SeedsDomainData;

abstract class TestCase extends BaseTestCase
{
    use AuthenticatesBloggers;
    use CreatesApplication;
    use RefreshDoctrineDatabase;
    use SeedsDomainData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->refreshDoctrineDatabase();
    }

    protected function tearDown(): void
    {
        $this->rollbackDoctrineDatabase();

        parent::tearDown();
    }
}
