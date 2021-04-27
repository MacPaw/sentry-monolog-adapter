<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Tests\Unit\LoggingStrategy;

use SentryMonologAdapter\Messenger\LoggingStrategy\LogAfterPositionStrategy;
use SentryMonologAdapter\Messenger\LoggingStrategy\LoggingStrategyInterface;
use SentryMonologAdapter\Tests\Unit\AbstractUnitTestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;

class LogAfterPositionStrategyTest extends AbstractUnitTestCase
{
    private LoggingStrategyInterface $loggingStrategy;

    private const ENVELOPE = 'envelope';
    private const WILL_LOG = 'willLog';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggingStrategy = new LogAfterPositionStrategy(2);
    }

    /**
     * @param Envelope $envelope
     * @param bool $willLog
     *
     * @dataProvider getLogAfterPositionStrategyDataProvider
     */
    public function testLogAfterPositionStrategy(
        Envelope $envelope,
        bool $willLog
    ): void {
        $willLogActual = $this->loggingStrategy->willLog($envelope);

        self::assertSame($willLog, $willLogActual);
    }

    public function getLogAfterPositionStrategyDataProvider(): array
    {
        return [
            [
                self::ENVELOPE => new Envelope(new stdClass(), [
                    new RedeliveryStamp(0)
                ]),
                self::WILL_LOG => false
            ],
            [
                self::ENVELOPE => new Envelope(new stdClass(), [
                    new RedeliveryStamp(1)
                ]),
                self::WILL_LOG => false
            ],
            [
                self::ENVELOPE => new Envelope(new stdClass(), [
                    new RedeliveryStamp(2)
                ]),
                self::WILL_LOG => true
            ],
            [
                self::ENVELOPE => new Envelope(new stdClass(), [
                    new RedeliveryStamp(3)
                ]),
                self::WILL_LOG => true
            ],
        ];
    }
}
