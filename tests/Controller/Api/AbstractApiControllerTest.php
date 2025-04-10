<?php

declare(strict_types=1);

namespace Tests\Controller\Api;

use DI\Container;
use Donchev\Framework\Controller\Api\AbstractApiController;
use Donchev\Framework\Exception\FrameworkException;
use PHPUnit\Framework\TestCase;

class ConcreteApiController extends AbstractApiController
{
    private Container $container;
    private string $mockInput = '';

    public function __construct(Container $container, string $input = '')
    {
        $this->container = $container;
        $this->mockInput = $input;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    protected function readInput(): string
    {
        return $this->mockInput;
    }
}

class PhpInputMock
{
    private static string $input = '';

    public static function set(string $data): void
    {
        self::$input = $data;
    }

    public function stream_open(): bool
    {
        return true;
    }

    public function stream_read(int $count): string
    {
        return substr(self::$input, 0, $count);
    }

    public function stream_eof(): bool
    {
        return true;
    }

    public function stream_stat(): array
    {
        return [];
    }
}

final class AbstractApiControllerTest extends TestCase
{
    protected function setUp(): void
    {
        if (in_array('php', stream_get_wrappers(), true)) {
            stream_wrapper_unregister('php');
        }
        stream_wrapper_register('php', PhpInputMock::class);
    }

    protected function tearDown(): void
    {
        if (!in_array('php', stream_get_wrappers(), true)) {
            stream_wrapper_restore('php');
        }
    }

    private function createController(array $nestedSettings, string $input): AbstractApiController
    {
        PhpInputMock::set($input);  // Ensure input is set for the test

        $container = $this->createMock(Container::class);
        $container->method('get')->willReturn([
            'api' => $nestedSettings
        ]);

        return new ConcreteApiController($container, $input);
    }

    public function testAuthorizeApiCallWithValidAuthAndJson(): void
    {
        $_SERVER['PHP_AUTH_USER'] = 'user';
        $_SERVER['PHP_AUTH_PW'] = 'pass';

        $controller = $this->createController([
            'username' => 'user',
            'password' => 'pass',
        ], '{"ok":true}');

        $result = $controller->authorizeApiCall();
        self::assertSame(['ok' => true], $result);
    }

    public function testAuthorizeApiCallFailsWithInvalidAuth(): void
    {
        $_SERVER['PHP_AUTH_USER'] = 'wrong';
        $_SERVER['PHP_AUTH_PW'] = 'wrong';

        $controller = $this->createController([
            'username' => 'user',
            'password' => 'pass',
        ], '{"ok":true}');

        $this->expectException(FrameworkException::class);
        $this->expectExceptionMessage('Authentication failed.');
        $controller->authorizeApiCall();
    }

    public function testAuthorizeApiCallFailsWithInvalidJson(): void
    {
        $_SERVER['PHP_AUTH_USER'] = 'user';
        $_SERVER['PHP_AUTH_PW'] = 'pass';

        $controller = $this->createController([
            'username' => 'user',
            'password' => 'pass',
        ], 'not-json');

        $this->expectException(FrameworkException::class);
        $this->expectExceptionMessage('Invalid payload.');
        $controller->authorizeApiCall();
    }
}
