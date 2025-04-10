<?php

declare(strict_types=1);

namespace Tests\Http;

use Donchev\Framework\Http\MiddlewareDispatcher;
use Donchev\Framework\Middleware\MiddlewareInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class MiddlewareDispatcherTest extends TestCase
{
    private function createRequestMock(): ServerRequestInterface
    {
        return $this->createMock(ServerRequestInterface::class);
    }

    private function createResponseMock(): ResponseInterface
    {
        return $this->createMock(ResponseInterface::class);
    }

    public function testHandleCallsControllerWhenNoMiddleware(): void
    {
        $request = $this->createRequestMock();
        $response = $this->createResponseMock();

        $controller = fn ($req) => $response;

        $dispatcher = new MiddlewareDispatcher([]);
        $result = $dispatcher->handle($request, $controller);

        self::assertSame($response, $result);
    }

    public function testHandleCallsSingleMiddleware(): void
    {
        $request = $this->createRequestMock();
        $response = $this->createResponseMock();

        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware->expects(self::once())
            ->method('process')
            ->with($request, self::callback(fn ($next) => is_callable($next)))
            ->willReturn($response);

        $dispatcher = new MiddlewareDispatcher([$middleware]);
        $result = $dispatcher->handle($request, fn ($r) => $this->fail('Controller should not be reached directly'));

        self::assertSame($response, $result);
    }

    public function testHandleChainsMultipleMiddlewareAndCallsController(): void
    {
        $request = $this->createRequestMock();
        $response = $this->createResponseMock();

        $log = [];

        $middleware1 = new class ($log) implements MiddlewareInterface {
            public function __construct(private array &$log)
            {
            }

            public function process(ServerRequestInterface $req, callable $next): ResponseInterface
            {
                $this->log[] = 'm1';
                return $next($req);
            }
        };

        $middleware2 = new class ($log) implements MiddlewareInterface {
            public function __construct(private array &$log)
            {
            }

            public function process(ServerRequestInterface $req, callable $next): ResponseInterface
            {
                $this->log[] = 'm2';
                return $next($req);
            }
        };

        $controller = function ($req) use (&$log, $response) {
            $log[] = 'controller';
            return $response;
        };

        $dispatcher = new MiddlewareDispatcher([$middleware1, $middleware2]);
        $result = $dispatcher->handle($request, $controller);

        self::assertSame($response, $result);
        self::assertSame(['m1', 'm2', 'controller'], $log);
    }
}
