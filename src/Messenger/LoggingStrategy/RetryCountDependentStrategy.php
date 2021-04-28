<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;

abstract class RetryCountDependentStrategy implements LoggingStrategyInterface
{
    public function getRetryCount(Envelope $envelope): int
    {
        return ($redeliveryStamp = $envelope->last(RedeliveryStamp::class)) instanceof RedeliveryStamp
            ? $redeliveryStamp->getRetryCount()
            : 0;
    }
}
