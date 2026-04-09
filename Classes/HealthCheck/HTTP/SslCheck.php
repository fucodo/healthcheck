<?php
declare(strict_types=1);

namespace fucodo\HealthCheck\HealthCheck\HTTP;

use fucodo\HealthCheck\HealthCheck\AbstractHealthCheck;
use Neos\Flow\Annotations as Flow;

class SslCheck extends AbstractHealthCheck
{
    protected const POSITION = 50;

    /**
     * @Flow\InjectConfiguration (package="Neos.Flow", path="http.baseUri")
     * @var string|null
     */
    protected $baseUri = null;

    protected function runCheckInternal(): void
    {
        $this->message = $this->baseUri;
        $this->message .= PHP_EOL . json_encode($this->checkUrl($this->baseUri));
        $this->markAsHealthy();
    }

    protected function checkUrl(string $uri): ?array
    {
        $ctx = stream_context_create(['ssl' => [
            'capture_session_meta' => true
        ]]);
        $data = file_get_contents($uri, false, $ctx);
        $json = json_decode($data);
        $meta = stream_context_get_options($ctx)['ssl']['session_meta'];

        return $meta;
    }

    public function getName(): string
    {
        return 'Check for SSL and TLS';
    }
}
