# HealthCheck for Neos Flow

This package provides a simple health check system for Neos Flow applications.
It allows you to define multiple health checks and run them via CLI.

## Installation

The package is usually installed as a distribution package. If you want to add it to your `composer.json`:

```json
{
    "require": {
        "fucodo/healthcheck": "@dev"
    }
}
```

## Usage

### CLI Command

You can run all registered health checks using the following command:

```bash
./flow health:check
```

The command will exit with code 0 if all checks are healthy, and code 1 if at least one check fails.

## Custom Health Checks

To create a custom health check, you need to implement `fucodo\HealthCheck\Domain\Service\HealthCheckInterface` or extend `fucodo\HealthCheck\HealthCheck\AbstractHealthCheck`.

The package automatically detects all implementations of `HealthCheckInterface` using Flow's reflection service.

### Execution Order (Priority)

The health checks are ordered by their **position** using the `Neos\Utility\PositionalArraySorter`. By default, `AbstractHealthCheck` returns `'500'`.

You can override the `getPosition()` method to control the execution order:

```php
    public function getPosition(): string
    {
        return '100'; // Run before default checks
    }
```

Lower values are executed first. You can also use positional strings like `before MyOtherCheck` if applicable, although simple numeric strings are most common.

### Example

```php
<?php
namespace Your\Package\HealthCheck;

use fucodo\HealthCheck\HealthCheck\AbstractHealthCheck;

class MyCustomCheck extends AbstractHealthCheck
{
    public function getName(): string
    {
        return 'My Custom Check';
    }

    protected function runCheckInternal(): void
    {
        // Your check logic here
        if ($everythingIsFine) {
            $this->markAsHealthy('All good!');
        } else {
            $this->markAsUnhealthy('Something went wrong.');
        }
    }
}
```

## Included Health Checks

- **Database Connection**: Checks if the database connection can be established.
- **Database Migrations**: Checks if there are pending database migrations.
- **Flow Context**: Shows the current Flow context (Development/Production/etc.).
