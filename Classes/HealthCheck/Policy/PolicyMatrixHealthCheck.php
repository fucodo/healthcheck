<?php

namespace fucodo\HealthCheck\HealthCheck\Policy;

use fucodo\HealthCheck\HealthCheck\AbstractHealthCheck;
use Neos\Flow\Security\Authorization\Privilege\PrivilegeInterface;
use Neos\Flow\Security\Authorization\PrivilegeManager;
use Neos\Flow\Security\Policy\Role;
use Neos\Flow\Annotations as Flow;

class PolicyMatrixHealthCheck extends AbstractHealthCheck implements \fucodo\HealthCheck\Domain\Service\HealthCheckBaselineGeneratorInterface
{
    /**
     * @Flow\Inject
     * @var \Neos\Flow\Security\Policy\PolicyService
     */
    protected $policyService;

    /**
     * @Flow\Inject
     * @var PrivilegeManager
     */
    protected $privilegeManager;

    protected const POSITION = 120;

    /**
     * @Flow\InjectConfiguration(type="Policy")
     * @var array
     */
    protected $policyConfiguration = [];

    public function getName(): string
    {
        return 'Policy Matrix check';
    }

    protected function runCheckInternal(): void
    {
        $this->markAsHealthy('currently we ask the roleservice for all grants, which is not optimal, as we do not see if abstained or denied');
    }

    protected function getPrivilegeTargetsTypes(): array
    {
        return array_keys($this->policyConfiguration['privilegeTargets']);
    }


    public function getState(): array
    {
        $grants = [];
        /** @var Role $role */
        foreach ($this->policyService->getRoles(true) as $role) {
            $grants[$role->getIdentifier()] = [];
            foreach ($this->getPrivilegeTargetsTypes() as $type) {
                $grants[$role->getIdentifier()][$type] = [];
                /** @var PrivilegeInterface $privilege */
                foreach ($this->policyService->getAllPrivilegesByType($type) as $privilege) {
                    $privilegeTargetIdentifier = $privilege->getPrivilegeTargetIdentifier();
                    $grants[$role->getIdentifier()][$type][$privilegeTargetIdentifier] = $this->privilegeManager->isPrivilegeTargetGrantedForRoles([$role], $privilegeTargetIdentifier) ? 'Y' : 'N';
                }
            }
        }
        return $grants;
    }
}
