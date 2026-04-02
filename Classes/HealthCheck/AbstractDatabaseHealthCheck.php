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



    public function getName(): string
    {
        return 'Database check ' . get_class($this);
    }



    protected function runCheckInternal(): void
    {
        $this->connection->executeQuery('SELECT 1');
    }

    public function getPosition(): string
    {
        return '100';
    }
}
