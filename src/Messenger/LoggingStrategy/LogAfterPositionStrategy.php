<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

use Symfony\Component\Messenger\Envelope;

class LogAfterPositionStrategy extends RetryCountDependentStrategy
{
    private int $position;

    public function __construct(int $position)
    {
        $this->position = $position;
    }

    public function willLog(Envelope $envelope): bool
    {
        $retryCount = $this->getRetryCount($envelope);

        return $retryCount >= $this->position;
    }
}
