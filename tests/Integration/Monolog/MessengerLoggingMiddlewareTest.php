<?php

declare(strict_types=1);

namespace SentryMonologAdapter\Tests\Integration\Monolog;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sentry\ClientInterface;
use Sentry\State\Hub;
use Sentry\State\Scope;
use SentryMonologAdapter\Messenger\LoggingStrategy\LoggingStrategyInterface;
use SentryMonologAdapter\Messenger\Middleware\MessengerLoggingMiddleware;
use SentryMonologAdapter\Tests\Fixtures\TestMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Middleware\StackMiddleware;
use Throwable;

class MessengerLoggingMiddlewareTest extends TestCase
{
    public function testNoExceptionOccurred(): void
    {
        $message = new TestMessage('test');
        $envelope = new Envelope($message);

        $loggingStrategy = $this->createMock(LoggingStrategyInterface::class);
        $loggingStrategy->expects(self::never())
            ->method('willLog');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::never())
            ->method('error');

        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::never())
            ->method('flush');

        $scope = new Scope();
        $hub = new Hub($client, $scope);


        $middleware = new MessengerLoggingMiddleware($hub, $logger);
        $middleware->addLoggingStrategy($loggingStrategy);

        $middleware->handle($envelope, $this->getStackMock());
    }

    public function testExceptionLogged(): void
    {
        $message = new TestMessage('test');
        $envelope = new Envelope($message);

        $loggingStrategy = $this->createMock(LoggingStrategyInterface::class);
        $loggingStrategy->expects(self::once())
            ->method('willLog')
            ->willReturn(true);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('error');

        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('flush');

        $scope = new Scope();
        $hub = new Hub($client, $scope);
        $handlerFailedException = new HandlerFailedException($envelope, [new Exception('test')]);

        $middleware = new MessengerLoggingMiddleware($hub, $logger);
        $middleware->addLoggingStrategy($loggingStrategy);

        try {
            $middleware->handle($envelope, $this->getStackMock($handlerFailedException));
        } catch (Throwable $exception) {
            self::assertSame($handlerFailedException, $exception);
        }
    }

    public function testExceptionNotLogged(): void
    {
        $message = new TestMessage('test');
        $envelope = new Envelope($message);

        $loggingStrategy = $this->createMock(LoggingStrategyInterface::class);
        $loggingStrategy->expects(self::once())
            ->method('willLog')
            ->willReturn(false);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::never())
            ->method('error');

        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::never())
            ->method('flush');

        $scope = new Scope();
        $hub = new Hub($client, $scope);
        $handlerFailedException = new HandlerFailedException($envelope, [new Exception('test')]);

        $middleware = new MessengerLoggingMiddleware($hub, $logger);
        $middleware->addLoggingStrategy($loggingStrategy);

        try {
            $middleware->handle($envelope, $this->getStackMock($handlerFailedException));
        } catch (Throwable $exception) {
            self::assertSame($handlerFailedException, $exception);
        }
    }

    public function testExceptionNotLoggedWithNoStrategies(): void
    {
        $message = new TestMessage('test');
        $envelope = new Envelope($message);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::never())
            ->method('error');

        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::never())
            ->method('flush');

        $scope = new Scope();
        $hub = new Hub($client, $scope);
        $handlerFailedException = new HandlerFailedException($envelope, [new Exception('test')]);

        $middleware = new MessengerLoggingMiddleware($hub, $logger);

        try {
            $middleware->handle($envelope, $this->getStackMock($handlerFailedException));
        } catch (Throwable $exception) {
            self::assertSame($handlerFailedException, $exception);
        }
    }

    protected function getStackMock(?Throwable $exception = null): StackMiddleware
    {
        $nextMiddleware = $this->createMock(MiddlewareInterface::class);

        if ($exception !== null) {
            $nextMiddleware
                ->expects(self::once())
                ->method('handle')
                ->willThrowException($exception);
        } else {
            $nextMiddleware
                ->expects(self::once())
                ->method('handle')
                ->willReturnCallback(function (Envelope $envelope, StackInterface $stack): Envelope {
                    return $envelope;
                });
        }

        return new StackMiddleware($nextMiddleware);
    }
}
