<?php

namespace HCH\ChatBotBundle\Service;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MetricsService
{
    private const METRICS_PREFIX = 'chatbot_metrics_';

    public function __construct(
        private AdapterInterface $cache,
        private RequestStack $requestStack,
        private array $config
    ) {}

    public function incrementMetric(string $metric, int $value = 1): void
    {
        $key = self::METRICS_PREFIX . $metric;
        $item = $this->cache->getItem($key);
        
        $currentValue = $item->get() ?? 0;
        $item->set($currentValue + $value);
        
        $this->cache->save($item);
    }

    public function recordResponseTime(float $duration): void
    {
        $key = self::METRICS_PREFIX . 'response_times';
        $item = $this->cache->getItem($key);
        
        $times = $item->get() ?? [];
        $times[] = $duration;
        
        // Garder uniquement les 1000 dernières mesures
        if (count($times) > 1000) {
            array_shift($times);
        }
        
        $item->set($times);
        $this->cache->save($item);
    }

    public function getMetrics(): array
    {
        $metrics = [
            'total_requests' => $this->getMetric('total_requests'),
            'successful_requests' => $this->getMetric('successful_requests'),
            'failed_requests' => $this->getMetric('failed_requests'),
            'average_response_time' => $this->calculateAverageResponseTime(),
        ];

        if ($this->config['metrics']['detailed']) {
            $metrics['requests_per_hour'] = $this->getRequestsPerHour();
            $metrics['error_rates'] = $this->getErrorRates();
        }

        return $metrics;
    }

    private function getMetric(string $metric): int
    {
        $item = $this->cache->getItem(self::METRICS_PREFIX . $metric);
        return $item->get() ?? 0;
    }

    private function calculateAverageResponseTime(): float
    {
        $item = $this->cache->getItem(self::METRICS_PREFIX . 'response_times');
        $times = $item->get() ?? [];
        
        if (empty($times)) {
            return 0.0;
        }

        return array_sum($times) / count($times);
    }
} 