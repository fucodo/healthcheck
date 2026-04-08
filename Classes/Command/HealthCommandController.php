<?php
declare(strict_types=1);

namespace fucodo\HealthCheck\Command;

use fucodo\HealthCheck\Domain\Service\BaseLineService;
use fucodo\HealthCheck\Domain\Service\HealthCheckService;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;

/**
 * @Flow\Scope("singleton")
 */
class HealthCommandController extends CommandController
{

    /**
     * @Flow\Inject
     * @var HealthCheckService
     */
    protected $diagnosticService = null;

    /**
     * @Flow\Inject
     * @var BaseLineService
     */
    protected $baseLineService = null;

    /**
     * @param $requiredArgument
     * @param $optionalArgument
     * @return void
     */
    public function checkCommand()
    {
        $exitCode = 0;

        $diagnostics = $this->diagnosticService->run();
        foreach ($diagnostics->getAllDiagnosis() as $diagnose) {
            if (!$diagnose instanceof \fucodo\HealthCheck\Domain\Service\HealthCheckInterface) {
                $this->outputLine('Unknown diagnostic - ' . get_class($diagnose));
                continue;
            }
            $healthy = true;
            if (!$diagnose->isHealthy()) {
                $healthy = false;
                $exitCode = 1;
            }
            $this->outputLine(
                sprintf(
                    '%s%s - %s%s',
                    $healthy ? '<success>' : '<error>',
                    $healthy ? '[✔]' : '[ⅹ]',
                    $diagnose->getName(),
                    $healthy ? '</success>' : '</error>',
                )
            );
            if ($diagnose->getMessage() !== '') {
                $this->outputFormatted($diagnose->getMessage() . PHP_EOL, [], 4);
            }
        }
        $this->sendAndExit($exitCode);
    }

    public function createBaselineCommand(): void
    {
        $exitCode = 0;

        $baselines = $this->baseLineService->run();
        foreach ($baselines->getAllDiagnosis() as $diagnose) {

            if (!$diagnose instanceof \fucodo\HealthCheck\Domain\Service\HealthCheckBaselineGeneratorInterface) {
                $this->outputLine('Unknown baseline - ' . get_class($diagnose));
                continue;
            }
            $healthy = $diagnose->isHealthy();
            $this->outputLine(
                sprintf(
                    '%s%s - %s%s',
                    $healthy ? '<success>' : '<error>',
                    $healthy ? '[✔]' : '[ⅹ]',
                    $diagnose->getName(),
                    $healthy ? '</success>' : '</error>',
                )
            );
            $this->baseLineService->persistHealthCheck($diagnose);
        }
        $this->sendAndExit($exitCode);
    }

    public function checkBaselineCommand(): void
    {
        $exitCode = 0;

        $baselines = $this->baseLineService->run();
        foreach ($baselines->getAllDiagnosis() as $diagnose) {
            $healthy = true;
            if (!$diagnose instanceof \fucodo\HealthCheck\Domain\Service\HealthCheckBaselineGeneratorInterface) {
                $this->outputLine('Unknown baseline - ' . get_class($diagnose));
                continue;
            }
            if (!$this->baseLineService->compareBaselines($diagnose)) {
                $exitCode = 1;
                $healthy = false;
            }
            $this->outputLine(
                sprintf(
                    '%s%s - %s%s',
                    $healthy ? '<success>' : '<error>',
                    $healthy ? '[✔]' : '[ⅹ]',
                    $diagnose->getName(),
                    $healthy ? '</success>' : '</error>',
                )
            );
        }
        $this->sendAndExit($exitCode);
    }
}
