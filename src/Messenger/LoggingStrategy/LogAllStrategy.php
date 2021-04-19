<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

class LogAllStrategy implements LoggingStrategyInterface
{
    public function willLog(int $retryCount): bool
    {
        return true;
    }
}
