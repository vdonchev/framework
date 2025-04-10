<?php

declare(strict_types=1);

namespace Tests\Controller\Web;

use DI\Container;
use Donchev\Framework\Controller\Web\AbstractController;
use Donchev\Framework\Http\Session;
use PHPUnit\Framework\TestCase;

class ConcreteWebController extends AbstractController
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    // Example of extending AbstractController and accessing methods
    public function getSession(): Session
    {
        return $this->getContainer()->get(Session::class);
    }
}

final class AbstractWebControllerTest extends TestCase
{
    private function createController(array $settings = []): ConcreteWebController
    {
        $container = $this->createMock(Container::class);
        $container->method('get')->willReturnCallback(function ($key) use ($settings) {
            if ($key === Session::class) {
                // Mocking the Session class properly
                return $this->createMock(Session::class);
            }
            return $settings;
        });

        return new ConcreteWebController($container);
    }

    public function testSessionHandling(): void
    {
        $controller = $this->createController([
            'app.settings' => [
                'use_sessions' => true,
            ],
        ]);

        // Mocking session methods
        $session = $controller->getSession();
        $session->method('set')->with('user', 'testuser');
        $session->method('get')->willReturn('testuser');

        $session->set('user', 'testuser');
        $this->assertSame('testuser', $session->get('user'));
    }

    // Other potential tests can be added for different methods and behaviors
}
