<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

class LogBeforeCountStrategy implements LoggingStrategyInterface
{
    private int $ignoredCount;

    public function __construct($ignoredCount)
    {
        $this->ignoredCount = $ignoredCount;
    }

    public function willLog(int $retryCount)
    {
        return $retryCount <= $this->ignoredCount;
    }
}