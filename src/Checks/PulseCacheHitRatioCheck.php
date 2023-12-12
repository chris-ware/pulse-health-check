<?php

namespace ChrisWare\PulseHealthCheck\Checks;

use Carbon\CarbonInterval;
use Laravel\Pulse\Facades\Pulse;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class PulseCacheHitRatioCheck extends Check
{
    protected float $warnWhenRatioBelow = 25;

    protected float $failWhenRatioBelow = 10;

    protected CarbonInterval $interval;

    public function failWhenSizeRatioBelow(float $failPercentage): self
    {
        $this->failWhenRatioBelow = $failPercentage;

        return $this;
    }

    public function warnWhenSizeRatioBelow(float $warnPercentage): self
    {
        $this->warnWhenRatioBelow = $warnPercentage;

        return $this;
    }

    public function interval(CarbonInterval $interval): static
    {
        $this->interval = $interval;

        return $this;
    }

    public function run(): Result
    {
        $cacheHits = Pulse::aggregateTypes(['cache_hit', 'cache_miss'], 'count', $this->interval ?? CarbonInterval::hour())
            ->map(function ($row) {
                return (object) [
                    'key' => $row->key,
                    'hits' => $row->cache_hit ?? 0,
                    'misses' => $row->cache_miss ?? 0,
                    'ratio' => ((int) ($row->cache_hit / ($row->cache_hit + $row->cache_miss) * 10000)) / 100,
                ];
            });
        $failRatio = $cacheHits->filter(fn ($row) => $row->ratio <= $this->failWhenRatioBelow);
        if ($failRatio->isNotEmpty()) {
            return Result::make()->failed("{$failRatio->count()} item(s) below {$this->failWhenRatioBelow}%");
        }

        $warnRatio = $cacheHits->filter(fn ($row) => $row->ratio < $this->warnWhenRatioBelow);
        if ($warnRatio->isNotEmpty()) {
            return Result::make()->warning("{$warnRatio->count()} item(s) below {$this->warnWhenRatioBelow}%");
        }

        return Result::make();
    }
}
