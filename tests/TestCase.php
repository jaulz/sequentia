<?php

namespace Jaulz\Sequentia\Tests;

use Jaulz\Sequentia\SequentiaServiceProvider;
use Tpetry\PostgresqlEnhanced\PostgresqlEnhancedServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SequentiaServiceProvider::class,
            PostgresqlEnhancedServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app) {
    }
}