<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Messenger\LoggingStrategy;

use Symfony\Component\Messenger\Envelope;

interface LoggingStrategyInterface
{
    public function willLog(Envelope $envelope): bool;
}
