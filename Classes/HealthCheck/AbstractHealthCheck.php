<?php
declare(strict_types=1);

namespace fucodo\HealthCheck\HealthCheck;

use fucodo\HealthCheck\Domain\Service\HealthCheckInterface;

abstract class AbstractHealthCheck implements HealthCheckInterface
{
    protected $healthy = null;

    protected string $message = '';

    protected const POSITION = 500;

    public function isHealthy(): bool
    {
        if ($this->healthy !== null) {
            return $this->healthy;
        }

        try {
            $this->runCheckInternal();
        }  catch (\Exception $e) {
            $this->healthy = false;
            $this->message = $e->getMessage();
        }
        return $this->healthy ?? false;
    }

    protected abstract function runCheckInternal(): void;

    protected function markAsHealthy(string $message = ''): void
    {
        $this->healthy = true;
        if ($message !== '') {
            $this->message = $message;
        }
    }

    protected function markAsUnhealthy(string $message = ''): void
    {
        $this->healthy = false;
        if ($message !== '') {
            $this->message = $message;
        }
    }

    public function getMessage(): string
    {
        return $this->message ?? 'No message';
    }

    public function getDetails(): array
    {
        return [];
    }

    public function getPosition(): string
    {
        return (string)static::POSITION;
    }
}
