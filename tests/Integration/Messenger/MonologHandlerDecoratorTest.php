<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Tests\Integration\Messenger;

use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;
use Sentry\ClientInterface;
use Sentry\Event;
use Sentry\Monolog\Handler;
use Sentry\State\Hub;
use Sentry\State\Scope;
use SentryMonologAdapter\Monolog\Handler\MonologHandlerDecorator;

class MonologHandlerDecoratorTest extends TestCase
{
    /**
     * @param LogRecord $record
     * @param array     $expectedExtra
     * @param array     $expectedTags
     *
     * @dataProvider handleDataProvider
     */
    public function testHandle(
        LogRecord $record,
        array $expectedExtra,
        array $expectedTags
    ): void {
        $scope = new Scope();
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('captureEvent')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->callback(function (Scope $scope) use ($expectedExtra, $expectedTags): bool {
                    $event = $scope->applyToEvent(Event::createEvent());

                    $this->assertNotNull($event);
                    $this->assertSame($expectedExtra, $event->getExtra());
                    $this->assertSame($expectedTags, $event->getTags());

                    return true;
                })
            );
        $hub = new Hub($client, $scope);
        $sentryHandler = new Handler($hub);

        $handler = new MonologHandlerDecorator($hub, $sentryHandler);
        $handler->handle($record);
    }

    public function handleDataProvider(): array
    {
        return [
            [
                new LogRecord(
                    new \DateTimeImmutable(),
                    'channel.test',
                    Level::Debug,
                    'test',
                    [
                        'extra' => [
                            'id' => 'test_id',
                            'message' => 'test_message'
                        ],
                        'tags' => [
                            'test_key' => 'test_value'
                        ]
                    ],
                    []
                ),
                [
                    'id' => 'test_id',
                    'message' => 'test_message',
                    'monolog.channel' => 'channel.test',
                    'monolog.level' => Logger::getLevelName(Logger::DEBUG),
                ],
                [
                    'test_key' => 'test_value'
                ]
            ],
            [
                new LogRecord(
                    new \DateTimeImmutable(),
                    'channel.test',
                    Level::Debug,
                    'test',
                    [],
                    []
                ),
                [
                    'monolog.channel' => 'channel.test',
                    'monolog.level' => Logger::getLevelName(Logger::DEBUG),
                ],
                []
            ]
        ];
    }
}
