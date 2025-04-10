<?php

declare(strict_types=1);

namespace Tests\Http;

use Donchev\Framework\Http\MiddlewareLoader;
use Donchev\Framework\Middleware\MiddlewareInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;

final class MiddlewareLoaderTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/middleware_test_' . uniqid();
        mkdir($this->tempDir);

        // Create dummy middleware class
        file_put_contents($this->tempDir . '/ValidMiddleware.php', '<?php
            namespace Tests\Fake\Middleware;
            use Donchev\Framework\Middleware\MiddlewareInterface;
            use Psr\Http\Message\ServerRequestInterface;
            use Psr\Http\Message\ResponseInterface;

            class ValidMiddleware implements MiddlewareInterface {
                public function process(ServerRequestInterface $r, callable $n): ResponseInterface {
                    return $n($r);
                }
            }
        ');

        // Create dummy non-middleware class
        file_put_contents($this->tempDir . '/NotMiddleware.php', '<?php
            namespace Tests\Fake\Middleware;

            class NotMiddleware {
            }
        ');
    }

    protected function tearDown(): void
    {
        foreach (glob($this->tempDir . '/*.php') as $file) {
            unlink($file);
        }
        rmdir($this->tempDir);
    }

    public function testLoadReturnsOnlyMiddlewareImplementations(): void
    {
        require_once $this->tempDir . '/ValidMiddleware.php';
        require_once $this->tempDir . '/NotMiddleware.php';

        $middlewareInstance = $this->createMock(MiddlewareInterface::class);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnCallback(function ($id) use ($middlewareInstance) {
            if ($id === 'app.settings') {
                return [
                    'middleware' => [
                        'namespace' => 'Tests\\Fake\\Middleware',
                        'dir' => $this->tempDir
                    ]
                ];
            }

            if ($id === 'Tests\\Fake\\Middleware\\ValidMiddleware') {
                return $middlewareInstance;
            }

            throw new RuntimeException('Unknown class: ' . $id);
        });

        $loader = new MiddlewareLoader($container);
        $result = $loader->load();

        self::assertCount(1, $result);
        self::assertSame($middlewareInstance, $result[0]);
    }
}
