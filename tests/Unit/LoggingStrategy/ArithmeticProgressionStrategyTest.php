<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Tests\Unit\LoggingStrategy;

use SentryMonologAdapter\Messenger\LoggingStrategy\ArithmeticProgressionStrategy;
use SentryMonologAdapter\Messenger\LoggingStrategy\LoggingStrategyInterface;
use SentryMonologAdapter\Tests\Unit\AbstractUnitTestCase;

class ArithmeticProgressionStrategyTest extends AbstractUnitTestCase
{
    private LoggingStrategyInterface $loggingStrategy;

    private const RETRY_COUNT = 'retryCount';
    private const WILL_LOG = 'willLog';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggingStrategy = new ArithmeticProgressionStrategy(2);
    }

    /**
     * @param int $retryCount
     * @param bool $willLog
     *
     * @dataProvider getPaymentEventDataProvider
     */
    public function testPaymentEvent(
        int $retryCount,
        bool $willLog
    ): void {
        $willLogActual = $this->loggingStrategy->willLog($retryCount);

        self::assertSame($willLog, $willLogActual);
    }

    public function getPaymentEventDataProvider(): array
    {
        return [
            [
                self::RETRY_COUNT => 0,
                self::WILL_LOG => true
            ],
            [
                self::RETRY_COUNT => 1,
                self::WILL_LOG => false
            ],
            [
                self::RETRY_COUNT => 2,
                self::WILL_LOG => true
            ],
            [
                self::RETRY_COUNT => 3,
                self::WILL_LOG => false
            ],
            [
                self::RETRY_COUNT => 4,
                self::WILL_LOG => true
            ]
        ];
    }
}
