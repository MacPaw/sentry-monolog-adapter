<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

class IgnoreBeginningLoggingStrategy implements LoggingStrategyInterface
{
    private int $interval;
    private int $ignoredCount;

    public function __construct(int $interval, int $ignoredCount)
    {
        $this->interval = $interval;
        $this->ignoredCount = $ignoredCount;
    }

    public function willLog(int $retryCount)
    {
        if($retryCount % $this->interval === 0 && $retryCount >= $this->ignoredCount){
            return true;
        }

        return false;
    }
}