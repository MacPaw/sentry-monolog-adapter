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
    public function testNoExceptionOccurred()
    {
        $message = new TestMessage('test');
        $envelope = new Envelope($message);

        $loggingStrategy = $this->createMock(LoggingStrategyInterface::class);
        $loggingStrategy->expects($this->never())
            ->method('willLog');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())
            ->method('error');

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->never())
            ->method('flush');

        $scope = new Scope();
        $hub = new Hub($client, $scope);


        $middleware = new MessengerLoggingMiddleware($hub, $logger);
        $middleware->addLoggingStrategy($loggingStrategy);

        $middleware->handle($envelope, $this->getStackMock());
    }

    public function testExceptionLogged()
    {
        $message = new TestMessage('test');
        $envelope = new Envelope($message);

        $loggingStrategy = $this->createMock(LoggingStrategyInterface::class);
        $loggingStrategy->expects($this->once())
            ->method('willLog')
            ->willReturn(true);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error');

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('flush');

        $scope = new Scope();
        $hub = new Hub($client, $scope);
        $handlerFailedException = new HandlerFailedException($envelope, [new Exception('test')]);

        $middleware = new MessengerLoggingMiddleware($hub, $logger);
        $middleware->addLoggingStrategy($loggingStrategy);

        try {
            $middleware->handle($envelope, $this->getStackMock($handlerFailedException));
        } catch (Throwable $exception) {
            $this->assertSame($handlerFailedException, $exception);
        }
    }

    public function testExceptionNotLogged()
    {
        $message = new TestMessage('test');
        $envelope = new Envelope($message);

        $loggingStrategy = $this->createMock(LoggingStrategyInterface::class);
        $loggingStrategy->expects($this->once())
            ->method('willLog')
            ->willReturn(false);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())
            ->method('error');

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->never())
            ->method('flush');

        $scope = new Scope();
        $hub = new Hub($client, $scope);
        $handlerFailedException = new HandlerFailedException($envelope, [new Exception('test')]);

        $middleware = new MessengerLoggingMiddleware($hub, $logger);
        $middleware->addLoggingStrategy($loggingStrategy);

        try {
            $middleware->handle($envelope, $this->getStackMock($handlerFailedException));
        } catch (Throwable $exception) {
            $this->assertSame($handlerFailedException, $exception);
        }
    }

    public function testExceptionNotLoggedWithNoStrategies()
    {
        $message = new TestMessage('test');
        $envelope = new Envelope($message);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())
            ->method('error');

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->never())
            ->method('flush');

        $scope = new Scope();
        $hub = new Hub($client, $scope);
        $handlerFailedException = new HandlerFailedException($envelope, [new Exception('test')]);

        $middleware = new MessengerLoggingMiddleware($hub, $logger);

        try {
            $middleware->handle($envelope, $this->getStackMock($handlerFailedException));
        } catch (Throwable $exception) {
            $this->assertSame($handlerFailedException, $exception);
        }
    }

    protected function getStackMock(?Throwable $exception = null)
    {
        $nextMiddleware = $this->createMock(MiddlewareInterface::class);

        if ($exception !== null) {
            $nextMiddleware
                ->expects($this->once())
                ->method('handle')
                ->willThrowException($exception);
        } else {
            $nextMiddleware
                ->expects($this->once())
                ->method('handle')
                ->willReturnCallback(function (Envelope $envelope, StackInterface $stack): Envelope {
                    return $envelope;
                });
        }

        return new StackMiddleware($nextMiddleware);
    }
}
