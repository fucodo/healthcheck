<?php

namespace fucodo\HealthCheck\Domain\Service;

interface HealthCheckWithStateInterface extends HealthCheckInterface
{
    public function getState(): array;
}
