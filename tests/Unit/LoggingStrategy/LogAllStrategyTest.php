<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Tests\Unit\LoggingStrategy;

use SentryMonologAdapter\Messenger\LoggingStrategy\LogAllStrategy;
use SentryMonologAdapter\Messenger\LoggingStrategy\LoggingStrategyInterface;
use SentryMonologAdapter\Tests\Fixtures\TestMessage;
use SentryMonologAdapter\Tests\Unit\AbstractUnitTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;

class LogAllStrategyTest extends AbstractUnitTestCase
{
    private LoggingStrategyInterface $loggingStrategy;

    private const ENVELOPE = 'envelope';
    private const WILL_LOG = 'willLog';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggingStrategy = new LogAllStrategy();
    }

    /**
     * @param Envelope $envelope
     * @param bool     $willLog
     *
     * @dataProvider getLogAllStrategyDataProvider
     */
    public function testLogAllStrategy(
        Envelope $envelope,
        bool $willLog
    ): void {
        $willLogActual = $this->loggingStrategy->willLog($envelope);

        self::assertSame($willLog, $willLogActual);
    }

    public function getLogAllStrategyDataProvider(): array
    {
        return [
            [
                self::ENVELOPE => new Envelope(new TestMessage('test'), [
                    new RedeliveryStamp(0)
                ]),
                self::WILL_LOG => true
            ],
            [
                self::ENVELOPE => new Envelope(new TestMessage('test'), [
                    new RedeliveryStamp(1)
                ]),
                self::WILL_LOG => true
            ],
            [
                self::ENVELOPE => new Envelope(new TestMessage('test'), [
                    new RedeliveryStamp(2)
                ]),
                self::WILL_LOG => true
            ],
            [
                self::ENVELOPE => new Envelope(new TestMessage('test'), [
                    new RedeliveryStamp(2)
                ]),
                self::WILL_LOG => true
            ],
        ];
    }
}
