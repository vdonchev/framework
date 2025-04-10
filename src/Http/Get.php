<?php

declare(strict_types=1);

namespace Donchev\Framework\Http;

class Get
{
    private array $getData;

    public function __construct()
    {
        $this->getData = filter_input_array(INPUT_GET, FILTER_UNSAFE_RAW) ?? [];
    }

    /**
     * @param string $key
     * @param bool $trim
     * @return mixed
     */
    public function get(string $key, bool $trim = true): mixed
    {
        $value = $this->getData[$key] ?? null;

        if ($trim && is_string($value)) {
            return trim($value);
        }

        return $value;
    }

    /**
     * @param bool $trim
     * @return array
     */
    public function getAll(bool $trim = true): array
    {
        if ($trim) {
            array_walk_recursive($this->getData, function (&$value) {
                if (is_string($value)) {
                    $value = trim($value);
                }
            });
        }

        return $this->getData;
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->getData);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->getData);
    }

    /**
     * @param array $keys
     * @return bool
     */
    public function hasAll(array $keys): bool
    {
        return empty(array_diff_key(array_flip($keys), $this->getData));
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isEmpty(string $key): bool
    {
        $value = $this->getData[$key] ?? null;
        return empty($value);
    }
}
