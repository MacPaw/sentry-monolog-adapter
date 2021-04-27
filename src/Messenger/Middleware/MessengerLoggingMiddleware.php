<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\Middleware;

use Psr\Log\LoggerInterface;
use Sentry\ClientInterface;
use Sentry\State\HubInterface;
use SentryMonologAdapter\Messenger\LoggingStrategy\LoggingStrategyInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Throwable;

class MessengerLoggingMiddleware implements MiddlewareInterface
{
    private HubInterface $hub;
    private LoggerInterface $logger;

    /**
     * @var array<LoggingStrategyInterface>
     */
    private array $loggingStrategies = [];

    public function __construct(
        HubInterface $hub,
        LoggerInterface $logger
    ) {
        $this->hub = $hub;
        $this->logger = $logger;
    }

    public function addLoggingStrategy(LoggingStrategyInterface $loggingStrategy): void
    {
        $this->loggingStrategies[] = $loggingStrategy;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            return $stack->next()->handle($envelope, $stack);
        } catch (Throwable $exception) {
            $this->logToSentry($exception);
            throw $exception;
        }
    }

    private function logToSentry(Throwable $exception): void
    {
        if (!($exception instanceof HandlerFailedException)) {
            return;
        }

        foreach ($this->loggingStrategies as $loggingStrategy) {
            if ($loggingStrategy->willLog($exception->getEnvelope())) {
                $this->logger->error(get_class($exception), [
                    'exception' => $exception,
                ]);

                $this->flushSentry();
                return;
            }
        }
    }

    private function flushSentry(): void
    {
        $client = $this->hub->getClient();
        if ($client instanceof ClientInterface) {
            $client->flush();
        }
    }
}
