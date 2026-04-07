<?php

namespace fucodo\HealthCheck\HealthCheck\Policy;

use fucodo\HealthCheck\HealthCheck\AbstractHealthCheck;
use Neos\Flow\Annotations as Flow;
class RolesExistingCheck extends AbstractHealthCheck
{

    /**
     * @Flow\Inject
     * @var \Neos\Flow\Security\Policy\PolicyService
     */
    protected $policyService;

    protected const POSITION = 110;

    public function getName(): string
    {
        return 'Policy, check existing roles';
    }


    protected function runCheckInternal(): void
    {
        $message = [];

        $roles = $this->policyService->getRoles();
        foreach ($roles as $role) {
            $message[] = $role->getIdentifier();
        }

        $this->markAsHealthy(implode(PHP_EOL, $message));
    }
}
