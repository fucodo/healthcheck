<?php

namespace fucodo\HealthCheck\HealthCheck;

use Doctrine\DBAL\Connection;
use Neos\Flow\Annotations as Flow;
use fucodo\HealthCheck\Domain\Service\HealthCheckInterface;

abstract class AbstractDatabaseHealthCheck extends AbstractHealthCheck implements HealthCheckInterface
{
    /**
     * @Flow\Inject
     * @var Connection
     */
    protected $connection;


    protected const POSITION = 100;
    public function getName(): string
    {
        return 'Database check ' . get_class($this);
    }
    protected function runCheckInternal(): void
    {
        $this->connection->executeQuery('SELECT 1');
    }
}
