<?php

namespace fucodo\HealthCheck\HealthCheck\Policy;

use fucodo\HealthCheck\HealthCheck\AbstractHealthCheck;
use Neos\Flow\Annotations as Flow;
class PrivilegeTargetsExistingCheck extends AbstractHealthCheck
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
        $targets = $this->policyService->getPrivilegeTargets();
        foreach ($targets as $target) {
            $message[] = $target->getIdentifier();
        }

        $this->markAsHealthy(implode(PHP_EOL, $message));
    }
}
