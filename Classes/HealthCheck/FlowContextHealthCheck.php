<?php
declare(strict_types=1);

namespace fucodo\HealthCheck\HealthCheck;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Utility\Environment;

class FlowContextHealthCheck extends AbstractHealthCheck
{
    /**
     * @Flow\Inject
     * @var Environment
     */
    protected $environment;

    public function getName(): string
    {
        return 'FLOW_CONTEXT';
    }

    protected function runCheckInternal(): void
    {
        $this->message = (string)$this->environment->getContext();
        $this->markAsHealthy();
    }

    public function getPosition(): string
    {
        return '100';
    }
}
