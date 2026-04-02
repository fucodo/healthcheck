<?php
declare(strict_types=1);

namespace fucodo\HealthCheck\Command;

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
            if (!$diagnose->isHealthy()) {
                $exitCode = 1;
            }
            $this->outputLine(
                sprintf(
                    '%s%s - %s%s',
                    $diagnose->isHealthy() ? '<success>' : '<error>',
                    $diagnose->isHealthy() ? '[✔]' : '[ⅹ]',
                    $diagnose->getName(),
                    $diagnose->isHealthy() ? '</success>' : '</error>',
                )
            );
            if ($diagnose->getMessage() !== '') {
                $this->outputFormatted($diagnose->getMessage() . PHP_EOL, [], 4);
            }
        }
        $this->sendAndExit($exitCode);
    }
}
