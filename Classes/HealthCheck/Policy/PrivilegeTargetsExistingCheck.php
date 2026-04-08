<?php

namespace fucodo\HealthCheck\HealthCheck\Policy;

use fucodo\HealthCheck\Domain\Service\HealthCheckBaselineGeneratorInterface;
use fucodo\HealthCheck\Domain\Service\HealthCheckInterface;
use fucodo\HealthCheck\HealthCheck\AbstractHealthCheck;
use Neos\Flow\Annotations as Flow;
class PrivilegeTargetsExistingCheck extends AbstractHealthCheck implements HealthCheckBaselineGeneratorInterface
{
    /**
     * @Flow\Inject
     * @var \Neos\Flow\Security\Policy\PolicyService
     */
    protected $policyService;

    protected const POSITION = 110;

    public function getName(): string
    {
        return 'Policy, check existing privilege targets';
    }

    protected function runCheckInternal(): void
    {
        $message = $this->getState();
        $this->markAsHealthy(implode(PHP_EOL, $message));
    }

    public function getState(): array
    {
        $message = [];
        $targets = $this->policyService->getPrivilegeTargets();
        foreach ($targets as $target) {
            $message[] = $target->getIdentifier();
        }
        return $message;
    }
}
