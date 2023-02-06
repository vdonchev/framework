<?php

namespace Donchev\Framework\Flash;

use Donchev\Framework\Http\Session;

class FlashService
{
    private const FLASH_KEY = 'flash';

    private const INFO = 'info';
    private const SUCCESS = 'success';
    private const WARNING = 'warning';
    private const ERROR = 'danger';

    public function addInfo($content)
    {
        $this->addFlash(self::INFO, $content);
    }

    public function addSuccess($content)
    {
        $this->addFlash(self::SUCCESS, $content);
    }

    public function addWarning($content)
    {
        $this->addFlash(self::WARNING, $content);
    }

    public function addError($content)
    {
        $this->addFlash(self::ERROR, $content);
    }

    public function getFlashes()
    {
        if (!Session::isSet(self::FLASH_KEY)) {
            return [];
        }

        $flash = Session::get(self::FLASH_KEY);
        Session::unset(self::FLASH_KEY);

        return $flash;
    }

    private function addFlash(string $type, $content)
    {
        $flash = Session::get(self::FLASH_KEY);

        if (!is_array($flash)) {
            $flash = [];
        }

        $flash[$type][] = $content;

        Session::set(self::FLASH_KEY, $flash);
    }
}
