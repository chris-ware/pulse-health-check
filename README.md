#  Pulse Health Checks

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chris-ware/pulse-health-check.svg?style=flat-square)](https://packagist.org/packages/chris-ware/pulse-health-check)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/chris-ware/pulse-health-check/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/chris-ware/pulse-health-check/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/chris-ware/pulse-health-check/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/chris-ware/pulse-health-check/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/chris-ware/pulse-health-check.svg?style=flat-square)](https://packagist.org/packages/chris-ware/pulse-health-check)

Combine the power of Laravel Pulse with Spatie's Health Checks

## Installation

You can install the package via composer:

```bash
composer require chris-ware/pulse-health-check
```

## Usage

### Cache Hit Ratio Check

This check will warn or fail if the cache hit ratio hits a certain percentage threshold. By default, it will fail at 10% hit ratio and warn at 25%.

```php
use ChrisWare\PulseHealthCheck\Checks\PulseCacheHitRatioCheck;

PulseCacheHitRatioCheck::new();
```

Configure the failure and warning levels:

```php
use ChrisWare\PulseHealthCheck\Checks\PulseCacheHitRatioCheck;

PulseCacheHitRatioCheck::new()->failWhenSizeRatioBelow(25)->warnWhenSizeRatioBelow(50);
```
### Generic Check

This generic check will accommodate most basic circumstances for Pulse aggregates. Every check must have a defined `for` method on it for it to understand which aggregate type to use.

Example for slow query aggregation:

```php
use ChrisWare\PulseHealthCheck\Checks\PulseCheck;

PulseCheck::new()
    ->for('slow_query')
    ->failWhenAbove(5)
    ->warnWhenAbove(3)
    ->interval(\Carbon\CarbonInterval::minutes(5));
```

Example for user request aggregation:

```php
use ChrisWare\PulseHealthCheck\Checks\PulseCheck;

PulseCheck::new()
    ->for('user_request')
    ->aggregate('count')
    ->failWhenAbove(500)
    ->warnWhenAbove(300)
    ->interval(\Carbon\CarbonInterval::minutes(5));
```

#### Available methods

**for**: Determine the aggregation type to use

**aggregate**: Determine the aggregation value to use (defaults to `max`)

**failWhenAbove**: Set the value to fail if greater than or equal to

**failWhenBelow**: Set the value to fail if less than or equal to

**warnWhenAbove**: Set the value to warn if greater than or equal to

**warnWhenBelow**: Set the value to warn if less than or equal to

**interval**: The CarbonInterval for aggregations to be evaluated (defaults to 1 hour)

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Chris Ware](https://github.com/chris-ware)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
