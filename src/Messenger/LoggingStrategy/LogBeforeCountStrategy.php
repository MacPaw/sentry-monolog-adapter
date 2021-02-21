<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

class LogBeforeCountStrategy implements LoggingStrategyInterface
{
    private int $ignoredCount;

    public function __construct(int $ignoredCount)
    {
        $this->ignoredCount = $ignoredCount;
    }

    public function willLog(int $retryCount): bool
    {
        return $retryCount <= $this->ignoredCount;
    }
}
