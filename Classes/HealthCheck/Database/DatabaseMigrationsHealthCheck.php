<?php

namespace fucodo\HealthCheck\HealthCheck\Database;

use fucodo\HealthCheck\Domain\Service\HealthCheckInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Doctrine\Service;
use fucodo\HealthCheck\HealthCheck\AbstractDatabaseHealthCheck;

class DatabaseMigrationsHealthCheck extends AbstractDatabaseHealthCheck implements HealthCheckInterface
{
    /**
     * @Flow\Inject
     * @var Service
     */
    protected $doctrineService;

    /**
     * @var array
     */
    protected $migrationStatus = [];

    public function getName(): string
    {
        return 'Database migration';
    }

    protected function runCheckInternal(): void
    {
        $this->migrationStatus = $this->doctrineService->getMigrationStatus();
        if ($this->migrationStatus['new'] > 0) {
            throw new \Exception('There are ' . $this->migrationStatus['new'] . ' pending migrations.');
        }
        $this->markAsHealthy();
    }

    public function getDetails(): array
    {
        return $this->migrationStatus;
    }
}
