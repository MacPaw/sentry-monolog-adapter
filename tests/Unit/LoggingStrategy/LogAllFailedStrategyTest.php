<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Tests\Unit\LoggingStrategy;

use SentryMonologAdapter\Messenger\LoggingStrategy\LogAllFailedStrategy;
use SentryMonologAdapter\Messenger\LoggingStrategy\LoggingStrategyInterface;
use SentryMonologAdapter\Tests\Fixtures\TestMessage;
use SentryMonologAdapter\Tests\Unit\AbstractUnitTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;

class LogAllFailedStrategyTest extends AbstractUnitTestCase
{
    private LoggingStrategyInterface $loggingStrategy;

    private const ENVELOPE = 'envelope';
    private const WILL_LOG = 'willLog';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggingStrategy = new LogAllFailedStrategy();
    }

    /**
     * @param Envelope $envelope
     * @param bool     $willLog
     *
     * @dataProvider getLogAllFailedStrategyDataProvider
     */
    public function testLogAllFailedStrategy(
        Envelope $envelope,
        bool $willLog
    ): void {
        $willLogActual = $this->loggingStrategy->willLog($envelope);

        self::assertSame($willLog, $willLogActual);
    }

    public function getLogAllFailedStrategyDataProvider(): array
    {
        return [
            [
                self::ENVELOPE => new Envelope(new TestMessage('test'), [
                    new SentToFailureTransportStamp('test')
                ]),
                self::WILL_LOG => true
            ],
            [
                self::ENVELOPE => new Envelope(new TestMessage('test'), []),
                self::WILL_LOG => false
            ]
        ];
    }
}
