<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Tests\Unit\LoggingStrategy;

use SentryMonologAdapter\Messenger\LoggingStrategy\LoggingStrategyInterface;
use SentryMonologAdapter\Messenger\LoggingStrategy\LogNothingStrategy;
use SentryMonologAdapter\Tests\Fixtures\TestMessage;
use SentryMonologAdapter\Tests\Unit\AbstractUnitTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;

class LogNothingStrategyTest extends AbstractUnitTestCase
{
    private LoggingStrategyInterface $loggingStrategy;

    private const ENVELOPE = 'envelope';
    private const WILL_LOG = 'willLog';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggingStrategy = new LogNothingStrategy();
    }

    /**
     * @param Envelope $envelope
     * @param bool     $willLog
     *
     * @dataProvider getLogNothingStrategyDataProvider
     */
    public function testLogNothingStrategy(
        Envelope $envelope,
        bool $willLog
    ): void {
        $willLogActual = $this->loggingStrategy->willLog($envelope);

        self::assertSame($willLog, $willLogActual);
    }

    public function getLogNothingStrategyDataProvider(): array
    {
        return [
            [
                self::ENVELOPE => new Envelope(new TestMessage('test'), [
                    new RedeliveryStamp(0)
                ]),
                self::WILL_LOG => false
            ],
            [
                self::ENVELOPE => new Envelope(new TestMessage('test'), [
                    new RedeliveryStamp(1)
                ]),
                self::WILL_LOG => false
            ],
            [
                self::ENVELOPE => new Envelope(new TestMessage('test'), [
                    new RedeliveryStamp(2)
                ]),
                self::WILL_LOG => false
            ],
            [
                self::ENVELOPE => new Envelope(new TestMessage('test'), [
                    new RedeliveryStamp(2)
                ]),
                self::WILL_LOG => false
            ],
        ];
    }
}
