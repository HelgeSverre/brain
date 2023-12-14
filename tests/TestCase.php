<?php

namespace HelgeSverre\Brain\Tests;

use Dotenv\Dotenv;
use HelgeSverre\Brain\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
            \OpenAI\Laravel\ServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        // Load .env.test into the environment.
        if (file_exists(dirname(__DIR__).'/.env')) {
            (Dotenv::createImmutable(dirname(__DIR__), '.env'))->load();
        }

        config()->set('openai.api_key', env('OPENAI_API_KEY'));
    }
}
