<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Monolog\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Sentry\Monolog\Handler;
use Sentry\State\HubInterface;
use Sentry\State\Scope;

class MonologHandlerDecorator extends AbstractProcessingHandler
{
    private Handler $sentryHandler;
    private HubInterface $hub;

    public function __construct(HubInterface $hub, Handler $sentryHandler)
    {
        $this->sentryHandler = $sentryHandler;
        $this->hub = $hub;
    }

    /**
     * @param array<array> $record
     */
    protected function write(array $record): void
    {
        $this->hub->withScope(function (Scope $scope) use ($record): void {
            if (isset($record['context']['extra']) && \is_array($record['context']['extra'])) {
                foreach ($record['context']['extra'] as $key => $value) {
                    $scope->setExtra((string) $key, $value);
                }
            }

            if (isset($record['context']['tags']) && \is_array($record['context']['tags'])) {
                foreach ($record['context']['tags'] as $key => $value) {
                    $scope->setTag($key, $value);
                }
            }

            $this->sentryHandler->handle($record);
        });
    }
}
