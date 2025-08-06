<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;

abstract class RetryCountDependentStrategy implements LoggingStrategyInterface
{
    protected function getRetryCount(Envelope $envelope): int
    {
        $redeliveryStamp = $envelope->last(RedeliveryStamp::class);

        return $redeliveryStamp instanceof RedeliveryStamp
            ? $redeliveryStamp->getRetryCount()
            : 0;
    }
}
