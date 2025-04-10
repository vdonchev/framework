<?php

declare(strict_types=1);

namespace Tests\Factory;

use Donchev\Framework\Factory\TwigFactory;
use Donchev\Framework\Http\Session;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Twig\Environment;

final class TwigFactoryTest extends TestCase
{
    private function createContainerMock(string $env, bool $useSessions): ContainerInterface
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method('get')->willReturnCallback(function ($id) use ($env, $useSessions) {
            if ($id === 'app.settings') {
                return [
                    'app' => [
                        'env' => $env,
                        'use_sessions' => $useSessions,
                    ]
                ];
            }

            if ($id === Session::class) {
                return new Session();
            }

            throw new RuntimeException('Unexpected container key: ' . $id);
        });

        return $container;
    }

    public function testCreateInProdEnv(): void
    {
        $container = $this->createContainerMock('prod', false);
        $twig = TwigFactory::create($container);

        self::assertInstanceOf(Environment::class, $twig);

        $globals = $twig->getGlobals();
        self::assertArrayHasKey('settings', $globals);
        self::assertArrayHasKey('http_get', $globals);
        self::assertArrayHasKey('http_post', $globals);
        self::assertArrayHasKey('http_cookie', $globals);
        self::assertArrayNotHasKey('http_session', $globals);
    }

    public function testCreateInDevEnvWithSessions(): void
    {
        $container = $this->createContainerMock('dev', true);
        $twig = TwigFactory::create($container);

        self::assertInstanceOf(Environment::class, $twig);

        $globals = $twig->getGlobals();
        self::assertArrayHasKey('http_session', $globals);

        $functions = $twig->getFunctions();
        $names = array_map(fn ($f) => $f->getName(), $functions);

        self::assertContains('get_flash', $names);
    }
}
