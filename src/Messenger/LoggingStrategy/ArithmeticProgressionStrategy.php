<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

use Symfony\Component\Messenger\Envelope;

class ArithmeticProgressionStrategy extends RetryCountDependentStrategy
{
    private int $step;

    public function __construct(int $step)
    {
        $this->step = $step;
    }

    public function willLog(Envelope $envelope): bool
    {
        $retryCount = $this->getRetryCount($envelope);

        return $retryCount % $this->step === 0;
    }
}
