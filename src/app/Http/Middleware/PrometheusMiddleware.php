<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;
use Prometheus\Exception\MetricNotFoundException;
use App\Services\PrometheusRegistry;

class PrometheusMiddleware
{
    protected CollectorRegistry $registry;

    public function __construct()
    {
        // Use in-memory adapter
        // $this->registry = new CollectorRegistry(new InMemory());

      $this->registry = PrometheusRegistry::getRegister();

    }

    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $duration = microtime(true) - $start;

        $method = $request->method();
        $route  = $request->route()?->uri() ?? $request->path();
        $status = (string) $response->getStatusCode();
        $namespace = 'app';

        // Counter
        try {
            $counter = $this->registry->getCounter($namespace, 'http_requests_total');
        } catch (MetricNotFoundException $e) {
            $counter = $this->registry->registerCounter(
                $namespace,
                'http_requests_total',
                'Total HTTP requests',
                ['method', 'route', 'status']
            );
        }

        $counter->inc([$method, $route, $status]);

        // Histogram
        try {
            $histogram = $this->registry->getHistogram($namespace, 'http_request_duration_seconds');
        } catch (MetricNotFoundException $e) {
            $histogram = $this->registry->registerHistogram(
                $namespace,
                'http_request_duration_seconds',
                'HTTP request duration in seconds',
                ['method', 'route', 'status'],
                [0.1, 0.3, 0.5, 1, 1.5, 2, 3, 5]
            );
        }

        $histogram->observe($duration, [$method, $route, $status]);

        return $response;
    }

    public function metrics()
    {
        $renderer = new RenderTextFormat();
        $metrics = $renderer->render($this->registry->getMetricFamilySamples());


        return response($metrics, 200)
            ->header('Content-Type', RenderTextFormat::MIME_TYPE);
    }
}
