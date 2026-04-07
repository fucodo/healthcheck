<?php

declare(strict_types=1);

namespace fucodo\HealthCheck\Domain\Service;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Reflection\ReflectionService;
use Neos\Utility\PositionalArraySorter;

class BaseLineService
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

        $testClasses = $this->getBaseLineGenerators();

        foreach ($testClasses as $testClass) {
            $healthChecks[] = $this->getInstance($testClass);
        }

        $healthChecks = (new PositionalArraySorter($healthChecks))->toArray();

        return HealthCheckResult::fromArray($healthChecks);
    }

    protected function getInstance(string $diagnosisClassName): HealthCheckBaselineGeneratorInterface
    {
        return $this->objectManager->get($diagnosisClassName);
    }

    public function getBaseLineGenerators(): array
    {
        $baselineGenerators = [];
        $baselineClassNames = $this->reflectionService->getAllImplementationClassNamesForInterface(HealthCheckBaselineGeneratorInterface::class);
        foreach ($baselineClassNames as $className) {
            if ($this->reflectionService->isClassAbstract($className)) {
                continue;
            }
            $baselineGenerators[] = $className;
        }
        return $baselineGenerators;
    }
}
