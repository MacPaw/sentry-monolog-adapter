<?php

declare(strict_types=1);

namespace SentrySymfony\Messenger\Middleware;

use Psr\Log\LoggerInterface;
use Sentry\ClientInterface;
use Sentry\State\HubInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Throwable;

class MessengerLoggingMiddleware implements MiddlewareInterface
{
    private HubInterface $hub;
    private LoggerInterface $logger;
    private int $ignoredRetries;

    public function __construct(HubInterface $hub, LoggerInterface $logger, $ignoredRetries)
    {
        $this->hub = $hub;
        $this->logger = $logger;
        $this->ignoredRetries = (int) $ignoredRetries;
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

        $redeliveryStamp  = $exception->getEnvelope()->last(RedeliveryStamp::class);
        $retryCount = $redeliveryStamp instanceof RedeliveryStamp ? $redeliveryStamp->getRetryCount() : 0;

        $this->logger->error(get_class($exception), [
            'exception' => $exception,
            'parameters' => ['retryCount' => $retryCount],
        ]);

        $this->flushSentry();
    }

    private function flushSentry(): void
    {
        $client = $this->hub->getClient();
        if ($client instanceof ClientInterface) {
            $client->flush();
        }
    }
}
