<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

class LogAllStrategy implements LoggingStrategyInterface
{
    private int $interval;

    public function __construct(int $interval)
    {
        $this->interval = $interval;
    }

    public function willLog(int $retryCount)
    {
        if ($retryCount % $this->interval) {
            return true;
        }

        return false;
    }
}
