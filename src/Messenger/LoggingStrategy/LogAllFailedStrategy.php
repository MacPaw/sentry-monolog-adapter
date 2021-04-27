<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;

class LogAllFailedStrategy implements LoggingStrategyInterface
{
    public function willLog(Envelope $envelope): bool
    {
        $failedStamp  = $envelope->last(SentToFailureTransportStamp::class);
        return $failedStamp instanceof SentToFailureTransportStamp;
    }
}
