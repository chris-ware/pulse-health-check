<?php

namespace ChrisWare\PulseHealthCheck\Checks;

use Carbon\CarbonInterval;
use Illuminate\Support\Number;
use Laravel\Pulse\Facades\Pulse;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class PulseCheck extends Check
{
    protected string $aggregate = 'max';

    protected bool $inverted = false;

    protected string $type;

    protected float $warningLevel;

    protected float $failureLevel;

    protected CarbonInterval $interval;

    public function for(string $type): static
    {
        $this->type = $type;

        $this->name(str($type)->title()->replace('_', '')->prepend('Pulse '));

        return $this;
    }

    public function failWhenAbove(float $level): static
    {
        $this->failureLevel = $level;
        $this->inverted = false;

        return $this;
    }

    public function failWhenBelow(float $level): static
    {
        $this->failureLevel = $level;
        $this->inverted = true;

        return $this;
    }

    public function warnWhenAbove(float $level): static
    {
        $this->warningLevel = $level;
        $this->inverted = false;

        return $this;
    }

    public function warnWhenBelow(float $level): static
    {
        $this->warningLevel = $level;
        $this->inverted = true;

        return $this;
    }

    public function interval(CarbonInterval $interval): static
    {
        $this->interval = $interval;

        return $this;
    }

    public function aggregate(string $aggregate): static
    {
        $this->aggregate = $aggregate;

        return $this;
    }

    public function run(): Result
    {
        $this->interval ??= CarbonInterval::hour();

        $value = Pulse::aggregate($this->type, $this->aggregate, $this->interval, $this->aggregate)->first()?->{$this->aggregate} ?? 0;
        $value = Number::format($value);

        $result = Result::make()->check($this);

        if ((! $this->inverted && $value >= $this->failureLevel) || ($this->inverted && $value <= $this->failureLevel)) {
            return $result->failed("{$value}");
        }

        if ((! $this->inverted && $value >= $this->warningLevel) || ($this->inverted && $value <= $this->warningLevel)) {
            return $result->warning("{$value}");
        }

        return $result->ok();

    }
}
