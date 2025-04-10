<?php

declare(strict_types=1);

namespace Tests\Http;

use Donchev\Framework\Http\Session;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

final class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    private function createStartedSession(): Session
    {
        $session = new Session();
        $ref = new ReflectionClass(Session::class);
        $prop = $ref->getProperty('started');
        $prop->setAccessible(true);
        $prop->setValue($session, true);
        return $session;
    }

    public function testSetAndGetValue(): void
    {
        $session = $this->createStartedSession();
        $session->set('user', 'John');
        self::assertSame('John', $_SESSION['user']);
        self::assertSame('John', $session->get('user'));
    }

    public function testGetReturnsNullWhenNotSet(): void
    {
        $session = $this->createStartedSession();
        self::assertNull($session->get('missing'));
    }

    public function testRemoveDeletesKey(): void
    {
        $session = $this->createStartedSession();
        $_SESSION['key'] = 'value';
        $session->remove('key');
        self::assertArrayNotHasKey('key', $_SESSION);
    }

    public function testDestroyClearsSession(): void
    {
        $_SESSION['x'] = 'value';

        $session = $this->createStartedSession();

        // Force the check inside destroy() to pass
        \session_start();

        $session->destroy();
        self::assertEmpty($_SESSION);
    }

    public function testGetThrowsIfNotStarted(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Session not started. Call start() first.');

        $session = new Session();
        $session->get('user');
    }

    public function testSetThrowsIfNotStarted(): void
    {
        $this->expectException(RuntimeException::class);
        $session = new Session();
        $session->set('user', 'test');
    }

    public function testRemoveThrowsIfNotStarted(): void
    {
        $this->expectException(RuntimeException::class);
        $session = new Session();
        $session->remove('x');
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
            $_SESSION = [];
        }
    }
}
