<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Tests\Integration\Processor;

use PHPUnit\Framework\TestCase;
use SentryMonologAdapter\Processor\ExceptionProcessor;
use SentryMonologAdapter\Tests\Fixtures\TestException;
use Symfony\Component\HttpFoundation\Response;

class ExceptionProcessorTest extends TestCase
{
    public function testExceptionProcessSuccess(): void
    {
        $exceptionProcessor = new ExceptionProcessor();

        $testException = new TestException(
            'testException',
            ['id' => 1, 'userName' => 'Yozhef'],
            Response::HTTP_BAD_REQUEST
        );

        $record = [
            'context' => [
                'exception' => $testException
            ]
        ];

        $result = $exceptionProcessor->__invoke($record);

        self::assertArrayHasKey('context', $result);
        self::assertArrayHasKey('exception', $result['context']);
        self::assertArrayHasKey('extra', $result['context']);
        self::assertArrayHasKey('message', $result);
        self::assertNotEmpty($result['context']['extra']);
    }
}
