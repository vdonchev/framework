<?php

declare(strict_types=1);

namespace Donchev\Framework\Factory;

use Donchev\Framework\Http\Session;

class SessionFactory
{
    public static function create(array $settings): Session
    {
        $session = new Session();

        if ($settings['app']['use_sessions']) {
            $session->start();
        }

        return $session;
    }
}
