<?php

namespace fucodo\HealthCheck\Domain\Service;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Reflection\ReflectionService;
use Neos\Utility\PositionalArraySorter;

/**
 * @Flow\Scope("singleton")
 */
class HealthCheckService
{
    /**
     * @Flow\Inject
     * @var ReflectionService
     */
    protected $reflectionService;

    /**
     * @Flow\Inject
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    public function run(): HealthCheckResult
    {
        $healthChecks = [];

        $testClasses = $this->getDiagnoses();

        foreach ($testClasses as $testClass) {
            $healthChecks[] = $this->getDiagnosisInstance($testClass);
        }

        $healthChecks = (new PositionalArraySorter($healthChecks))->toArray();

        return HealthCheckResult::fromArray($healthChecks);
    }

    protected function getDiagnosisInstance(string $diagnosisClassName): HealthCheckInterface
    {
        return $this->objectManager->get($diagnosisClassName);
    }

    /**
     * @return HealthCheckInterface[]
     */
    protected function getDiagnoses(): array
    {
        $diagnoses = [];
        $diagnosisClassNames = $this->reflectionService->getAllImplementationClassNamesForInterface(HealthCheckInterface::class);
        foreach ($diagnosisClassNames as $className) {
            if ($this->reflectionService->isClassAbstract($className)) {
                continue;
            }
            $diagnoses[] = $className;
        }
        return $diagnoses;
    }
}
