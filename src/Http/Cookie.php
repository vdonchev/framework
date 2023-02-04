<?php

namespace Donchev\Framework\Http;

class Cookie
{
    const DAY = 86400;
    const WEEK = 604800;
    const MONTH = 2592000;
    const YEAR = 31536000;

    const DEFAULT_COOKIE_OPTIONS = [
        'expiry' => self::MONTH,
        'path' => '/',
        'domain' => true,
        'secure' => false,
        'httponly' => false,
        'remove_global' => true
    ];

    public static function day(int $amount = 1): int
    {
        return self::calculate($amount, self::DAY);
    }

    public static function week(int $amount = 1): int
    {
        return self::calculate($amount, self::WEEK);
    }

    public static function month(int $amount = 1): int
    {
        return self::calculate($amount, self::MONTH);
    }

    public static function year(int $amount = 1): int
    {
        return self::calculate($amount, self::YEAR);
    }

    private static function calculate(int $amount, int $duration): int
    {
        return ($duration * $amount);
    }

    public static function exist(string $key): bool
    {
        return (isset($_COOKIE[$key]));
    }

    public static function isEmpty(string $key): bool
    {
        return (empty($_COOKIE[$key]));
    }

    public static function get(string $key): ?string
    {
        return self::exist($key) ? $_COOKIE[$key] : null;
    }

    public static function set(string $key, $value, $options = []): bool
    {
        $set = false;

        $defaultOptions = self::DEFAULT_COOKIE_OPTIONS;
        if (!headers_sent()) {

            foreach ($defaultOptions as $optionKey => $optionValue) {
                if (!array_key_exists($optionKey, $options)) {
                    $options[$optionKey] = $defaultOptions[$optionValue];
                }
            }

            $options['domain'] = $options['domain'] === true ? '.' . $_SERVER['HTTP_HOST'] : '';
            $options['expiry'] =
                is_numeric($options['expiry']) ? $options['expiry'] += time() : strtotime($options['expiry']);

            $set = setcookie(
                $key,
                $value,
                $options['expiry'],
                $options['path'],
                $options['domain'],
                $options['secure'],
                $options['httponly']
            );

            if ($set) {
                $_COOKIE[$key] = $value;
            }
        }

        return $set;
    }

    public static function unset(string $key, $options = []): bool
    {
        $defaultOptions = self::DEFAULT_COOKIE_OPTIONS;

        $unset = false;

        if (!headers_sent()) {
            foreach ($defaultOptions as $optionKey => $optionValue) {
                if (!array_key_exists($optionKey, $options)) {
                    $options[$optionKey] = $defaultOptions[$optionValue];
                }
            }

            $options['domain'] = $options['domain'] === true ? '.' . $_SERVER['HTTP_HOST'] : '';

            if ($options['remove_global']) {
                unset($_COOKIE[$key]);
            }

            $unset = setcookie(
                $key, '',
                (time() - 3600),
                $options['path'],
                $options['domain'],
                $options['secure'],
                $options['httponly']
            );
        }

        return $unset;
    }
}
