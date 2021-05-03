<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Processor;

use Throwable;

class ExceptionProcessor
{
    /**
     * @param array<string, mixed> $record
     *
     * @return array[]
     */
    public function __invoke(array $record): array
    {
        if (
            !array_key_exists('exception', $record['context'])
            || !($record['context']['exception'] instanceof Throwable)
        ) {
            return $record;
        }

        $exception = $record['context']['exception'];
        $record['message'] = $exception->getMessage();

        if (array_key_exists('parameters', $record['context'])) {
            $record = $this->setExtra($record, $record['context']['parameters']);
        }

        return $record;
    }

    /**
     * setExtra.
     *
     * @param array[] $record
     * @param array[] $parameters
     *
     * @return array[]
     */
    private function setExtra(array $record, array $parameters): array
    {
        if (!array_key_exists('extra', $record['context'])) {
            $record['context']['extra'] = [];
        }

        $record['context']['extra'] = array_merge($record['context']['extra'], $parameters);

        return $record;
    }
}
