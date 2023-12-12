<?php

namespace ChrisWare\PulseHealthCheck\Checks;

use Carbon\CarbonInterval;
use Illuminate\Support\Number;
use Laravel\Pulse\Facades\Pulse;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class PulseCheck extends Check
{
    protected string $field;

    protected bool $inverted = false;

    protected string $type;

    protected int $warningLevel = 1000;

    protected int $failureLevel = 500;

    protected CarbonInterval $interval;

    public function for(string $type): static
    {
        $this->type = $type;

        $this->name(str($type)->title()->replace('_', '')->prepend('Pulse '));

        return $this;
    }

    public function failureLevel(int $max): static
    {
        $this->failureLevel = $max;

        return $this;
    }

    public function warningLevel(int $max): static
    {
        $this->warningLevel = $max;

        return $this;
    }

    public function interval(CarbonInterval $interval): static
    {
        $this->interval = $interval;

        return $this;
    }

    public function byMax(): static
    {
        $this->field = 'max';

        return $this;
    }

    public function byCount(): static
    {
        $this->field = 'count';

        return $this;
    }

    public function inverted(): static
    {
        $this->inverted = true;

        return $this;
    }

    public function run(): Result
    {
        $this->interval ??= CarbonInterval::hours(24);

        $this->field ??= 'max';

        $value = Pulse::aggregate($this->type, $this->field, $this->interval, $this->field)->first()?->{$this->field} ?? 0;
        $value = Number::format($value);

        $result = Result::make()->check($this);

        if ($value >= $this->failureLevel || ($this->inverted && ($value <= $this->failureLevel))) {
            return $result->failed("{$value}");
        }

        if ($value >= $this->warningLevel || ($this->inverted && ($value <= $this->warningLevel))) {
            return $result->warning("{$value}");
        }

        return $result->ok();

    }
}
