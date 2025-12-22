<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis;

class PrometheusServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CollectorRegistry::class, function () {
            Redis::setDefaultOptions([
                'host' => env('REDIS_HOST', 'redis'),
                'port' => env('REDIS_PORT', 6379),
                'timeout' => 0.1,
                'read_timeout' => 10,
                'persistent_connections' => false,
            ]);

            return new CollectorRegistry(new Redis());
        });
    }
}
