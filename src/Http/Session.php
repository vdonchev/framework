<?php

declare(strict_types=1);

namespace Donchev\Framework\Http;

use RuntimeException;

class Session
{
    private bool $started = false;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->started = true;
        }
    }

    public function start(): void
    {
        if ($this->started) {
            return;
        }

        ini_set('session.cookie_httponly', '1');
        ini_set('session.use_strict_mode', '1');

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            ini_set('session.cookie_secure', '1');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            $this->started = true;
        }
    }

    public function get(string $key): mixed
    {
        $this->ensureStarted();

        return $_SESSION[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->ensureStarted();

        $_SESSION[$key] = $value;
    }

    public function remove(string $key): void
    {
        $this->ensureStarted();

        unset($_SESSION[$key]);
    }

    public function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();
            $this->started = false;
        }
    }

    private function ensureStarted(): void
    {
        if (!$this->started) {
            throw new RuntimeException('Session not started. Call start() first.');
        }
    }
}
