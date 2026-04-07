<?php

namespace fucodo\HealthCheck\HealthCheck\Database;

use fucodo\HealthCheck\Domain\Service\HealthCheckInterface;
use fucodo\HealthCheck\HealthCheck\AbstractDatabaseHealthCheck;

class DatabaseConnectionCheck extends AbstractDatabaseHealthCheck implements HealthCheckInterface
{
    public function getName(): string
    {
        return 'Database connection';
    }

    protected function runCheckInternal(): void
    {
        $this->connection->executeQuery('SELECT 1');
        $this->markAsHealthy();
    }
}
