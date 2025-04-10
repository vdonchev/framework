<?php

declare(strict_types=1);

namespace Tests\Flash;

use Donchev\Framework\Flash\FlashService;
use Donchev\Framework\Http\Session;
use PHPUnit\Framework\TestCase;

final class FlashServiceTest extends TestCase
{
    private Session $session;

    protected function setUp(): void
    {
        $_SESSION = [];
        $this->session = new Session();
        $this->session->start();
    }

    public function testAddInfoAndRetrieve(): void
    {
        $flash = new FlashService($this->session);
        $flash->addInfo('Info message');
        $messages = $flash->getFlashes();

        self::assertArrayHasKey('info', $messages);
        self::assertContains('Info message', $messages['info']);
    }

    public function testAddMultipleSuccessMessages(): void
    {
        $flash = new FlashService($this->session);
        $flash->addSuccess(['Saved', 'Done']);
        $messages = $flash->getFlashes();

        self::assertArrayHasKey('success', $messages);
        self::assertSame(['Saved', 'Done'], $messages['success']);
    }

    public function testAddWarningAndError(): void
    {
        $flash = new FlashService($this->session);
        $flash->addWarning('Be careful');
        $flash->addError(['Bad', 'Very bad']);
        $messages = $flash->getFlashes();

        self::assertArrayHasKey('warning', $messages);
        self::assertArrayHasKey('danger', $messages);
        self::assertSame(['Be careful'], $messages['warning']);
        self::assertSame(['Bad', 'Very bad'], $messages['danger']);
    }

    public function testFlashesAreClearedAfterRetrieval(): void
    {
        $flash = new FlashService($this->session);
        $flash->addInfo('One-time message');
        $messages = $flash->getFlashes();

        self::assertArrayHasKey('info', $messages);
        $secondRead = $flash->getFlashes();
        self::assertEmpty($secondRead);
    }

    public function testGetFlashesReturnsEmptyArrayIfNone(): void
    {
        $flash = new FlashService($this->session);
        $messages = $flash->getFlashes();
        self::assertIsArray($messages);
        self::assertEmpty($messages);
    }
}
