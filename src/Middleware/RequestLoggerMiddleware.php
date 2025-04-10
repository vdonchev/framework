<?php

declare(strict_types=1);

namespace Donchev\Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class RequestLoggerMiddleware implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function process(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        $this->logger->info('Request processed trough Middleware class');
        return $next($request);
    }
}
