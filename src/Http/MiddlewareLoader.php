<?php

declare(strict_types=1);

namespace Donchev\Framework\Http;

use Donchev\Framework\Middleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Throwable;

class MiddlewareLoader
{
    private mixed $middlewareNamespace;
    private mixed $middlewareDir;

    public function __construct(
        private ContainerInterface $container,
        ?string $middlewareNamespace = null,
        ?string $middlewareDir = null,
    ) {
        $settings = $container->get('app.settings');

        $this->middlewareNamespace = $middlewareNamespace ?? $settings['middleware']['namespace'];
        $this->middlewareDir = $middlewareDir ?? $settings['middleware']['dir'];
    }

    /**
     * @return MiddlewareInterface[]
     */
    public function load(): array
    {
        $middlewareList = [];

        $files = glob($this->middlewareDir . '/*.php');
        foreach ($files as $file) {
            $className = $this->namespaceFromFile($file);

            if (!class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);

            if (!$reflection->implementsInterface(MiddlewareInterface::class)) {
                continue;
            }

            try {
                $middleware = $this->container->get($className);
                $middlewareList[] = $middleware;
            } catch (Throwable $e) {
                // log or skip silently
            }
        }

        return $middlewareList;
    }

    private function namespaceFromFile(string $file): string
    {
        $filename = pathinfo($file, PATHINFO_FILENAME);
        return rtrim($this->middlewareNamespace, '\\') . '\\' . $filename;
    }
}
