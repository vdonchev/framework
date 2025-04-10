<?php

declare(strict_types=1);

namespace Donchev\Framework\Http;

use Donchev\Framework\Middleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewareDispatcher
{
    /** @var MiddlewareInterface[] */
    private array $middlewareStack;

    public function __construct(array $middlewareStack = [])
    {
        $this->middlewareStack = $middlewareStack;
    }

    public function handle(ServerRequestInterface $request, callable $controller): ResponseInterface
    {
        $middlewareStack = $this->middlewareStack;
        if (empty($this->middlewareStack)) {
            return $controller($request);
        }

        $next = function (ServerRequestInterface $req) use (&$middlewareStack, $controller, &$next): ResponseInterface {
            if (empty($middlewareStack)) {
                return $controller($req);
            }

            $middleware = array_shift($middlewareStack);
            return $middleware->process($req, fn ($r) => $next($r));
        };

        return $next($request);
    }
}
