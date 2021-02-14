<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Monolog\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Sentry\Monolog\Handler;
use Sentry\State\Scope;

use function Sentry\withScope;

class MonologHandlerDecorator extends AbstractProcessingHandler
{
    private Handler $sentryHandler;

    public function __construct(Handler $sentryHandler)
    {
        $this->sentryHandler = $sentryHandler;
    }

    protected function write(array $record): void
    {
        withScope(function (Scope $scope) use ($record): void {
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
