<?php

namespace fucodo\HealthCheck\Domain\Service;

interface HealthCheckInterface
{
    public function getName(): string;

    public function isHealthy(): bool;

    public function getMessage(): string;

    public function getDetails(): array;

    public function getPosition(): string;
}
