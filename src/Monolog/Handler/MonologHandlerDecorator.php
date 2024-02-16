<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Monolog\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Sentry\Monolog\Handler;
use Sentry\State\HubInterface;
use Sentry\State\Scope;

use function Sentry\withScope;

class MonologHandlerDecorator extends AbstractProcessingHandler
{
    private Handler $sentryHandler;
    private HubInterface $hub;

    public function __construct(HubInterface $hub, Handler $sentryHandler)
    {
        $this->sentryHandler = $sentryHandler;
        $this->hub = $hub;
        parent::__construct();
    }

    /**
     * @phpstan-param LogRecord $record
     */
    protected function write(LogRecord $record): void
    {
        $this->hub->withScope(function (Scope $scope) use ($record): void {
            $context = $record->context;

            if (isset($context['extra']) && \is_array($context['extra'])) {
                foreach ($context['extra'] as $key => $value) {
                    $scope->setExtra((string) $key, $value);
                }
            }

            if (isset($context['tags']) && \is_array($context['tags'])) {
                foreach ($context['tags'] as $key => $value) {
                    $scope->setTag($key, $value);
                }
            }

            $this->sentryHandler->handle($record);
        });
    }
}
