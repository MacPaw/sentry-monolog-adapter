<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

class IgnoreEndingLoggingStrategy implements LoggingStrategyInterface
{
    private int $interval;
    private int $loggedCount;

    public function __construct(int $interval, int $loggedCount)
    {
        $this->interval = $interval;
        $this->loggedCount = $loggedCount;
    }

    public function willLog(int $retryCount)
    {
        if ($retryCount % $this->interval === 0 && $retryCount < $this->loggedCount) {
            return true;
        }

        return false;
    }
}
