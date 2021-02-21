<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

class ArithmeticProgressionStrategy implements LoggingStrategyInterface
{
    private int $step;

    public function __construct($step)
    {
        $this->step = $step;
    }

    public function willLog(int $retryCount)
    {
        return $retryCount % $this->step === 0;
    }
}