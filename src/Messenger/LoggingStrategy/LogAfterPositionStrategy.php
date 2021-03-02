<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

class LogAfterPositionStrategy implements LoggingStrategyInterface
{
    private int $position;

    public function __construct(int $position)
    {
        $this->position = $position;
    }

    public function willLog(int $retryCount): bool
    {
        return $retryCount >= $this->position;
    }
}
