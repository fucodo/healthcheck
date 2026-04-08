<?php

declare(strict_types=1);

namespace fucodo\HealthCheck\Domain\Service;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Reflection\ReflectionService;
use Neos\Utility\Files;
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




    public function persistHealthCheck(HealthCheckWithStateInterface $healthCheckResult): void
    {
        file_put_contents(
            $this->getFileName($healthCheckResult),
            $this->buildBaselineContent($healthCheckResult)
        );
    }

    protected function getFileName(HealthCheckWithStateInterface $healthCheckResult): string
    {
        $fileName = FLOW_PATH_DATA . 'HealthChecks/' .str_replace('\\', '_', get_class($healthCheckResult)) . '.json';
        Files::createDirectoryRecursively(dirname($fileName));
        return $fileName;
    }

    protected function buildBaselineContent(HealthCheckWithStateInterface $healthCheckResult): string
    {
        return json_encode(
            [
                'class' => get_class($healthCheckResult),
                'name' => $healthCheckResult->getName(),
                'heathy' => $healthCheckResult->isHealthy(),
                'state' => $healthCheckResult->getState()
            ],
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
        );
    }

    public function compareBaselines(HealthCheckWithStateInterface $healthCheckResult): bool
    {
        return file_get_contents($this->getFileName($healthCheckResult)) === $this->buildBaselineContent($healthCheckResult);
    }
}
