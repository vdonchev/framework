<?php

declare(strict_types=1);

namespace Donchev\Framework\Flash;

use Donchev\Framework\Http\Session;

class FlashService
{
    private const FLASH_KEY = 'flash';

    private const INFO = 'info';
    private const SUCCESS = 'success';
    private const WARNING = 'warning';
    private const ERROR = 'danger';

    public function __construct(private Session $session)
    {
    }

    public function addInfo(string|array $content): void
    {
        if (is_array($content)) {
            $this->addFlashes(self::INFO, $content);
            return;
        }

        $this->addFlash(self::INFO, $content);
    }

    public function addSuccess(string|array $content): void
    {
        if (is_array($content)) {
            $this->addFlashes(self::SUCCESS, $content);
            return;
        }

        $this->addFlash(self::SUCCESS, $content);
    }

    public function addWarning(string|array $content): void
    {
        if (is_array($content)) {
            $this->addFlashes(self::WARNING, $content);
            return;
        }

        $this->addFlash(self::WARNING, $content);
    }

    public function addError(string|array $content): void
    {
        if (is_array($content)) {
            $this->addFlashes(self::ERROR, $content);
            return;
        }

        $this->addFlash(self::ERROR, $content);
    }

    public function getFlashes(): array
    {
        if (!$this->session->get(self::FLASH_KEY)) {
            return [];
        }

        $flash = $this->session->get(self::FLASH_KEY);
        $this->session->remove(self::FLASH_KEY);

        return $flash;
    }

    private function addFlash(string $type, $content): void
    {
        $flash = $this->session->get(self::FLASH_KEY);

        if (!is_array($flash)) {
            $flash = [];
        }

        $flash[$type][] = $content;

        $this->session->set(self::FLASH_KEY, $flash);
    }

    private function addFlashes(string $type, array $contents): void
    {
        array_map([$this, 'addFlash'], array_fill(0, count($contents), $type), $contents);
    }
}
