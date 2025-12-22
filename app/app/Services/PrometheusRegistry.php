<?php

namespace App\Services;

use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis;
use Prometheus\Storage\InMemory;

class PrometheusRegistry
{
    private static ?CollectorRegistry $registry = null;

    public static function getRegister(): CollectorRegistry
    {
        if (!self::$registry) {
            $adapter = new Redis([
              'host' => env('REDIS_HOST', '192.168.0.104'),
              'port' => env('REDIS_PORT','6379'),
              'password' => env('REDIS_PASSWORD', null),
              'timeout' => env('REDIS_TIMEOUT', 0.1),
              'read_timeout' => env('REDIS_READ_TIMEOUT', 10),
              'persistent_connections' => false
            ]);

            self::$registry = new CollectorRegistry($adapter);
        }

        return self::$registry;
    }


    public static function get(): CollectorRegistry
    {
        if (!self::$registry) {
            self::$registry = new CollectorRegistry(new InMemory());
        }

        return self::$registry;
    }
}
