<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

interface LoggingStrategyInterface
{
    public function willLog(int $retryCount): bool;
}
