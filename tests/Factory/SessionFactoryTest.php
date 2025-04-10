<?php

declare(strict_types=1);

namespace Tests\Factory;

use Donchev\Framework\Factory\SessionFactory;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class SessionFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public function testCreateStartsSessionIfEnabled(): void
    {
        $session = SessionFactory::create(['app' => ['use_sessions' => true]]);
        $session->set('foo', 'bar');
        self::assertSame('bar', $session->get('foo'));
    }

    public function testCreateDoesNotStartSessionIfDisabled(): void
    {
        $session = SessionFactory::create(['app' => ['use_sessions' => false]]);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Session not started. Call start() first.');
        $session->get('test');
    }
}
