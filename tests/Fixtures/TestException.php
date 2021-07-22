<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Tests\Fixtures;

use Exception;
use Throwable;

class TestException extends Exception implements Throwable
{
    /**
     * @var iterable[]
     */
    private array $parameters;

    /**
     * @param string         $message
     * @param iterable[]     $parameters
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message,
        array $parameters,
        int $code = 0,
        Throwable $previous = null
    ) {
        $this->parameters = $parameters;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return iterable[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
