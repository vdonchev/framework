<?php

declare(strict_types=1);

namespace Donchev\Framework\Http;

class Post
{
    private array $postData;

    public function __construct()
    {
        $this->postData = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW) ?? [];
    }

    /**
     * @param string $key
     * @param bool $trim
     * @return mixed
     */
    public function get(string $key, bool $trim = true): mixed
    {
        $value = $this->postData[$key] ?? null;

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
            array_walk_recursive($this->postData, function (&$value) {
                if (is_string($value)) {
                    $value = trim($value);
                }
            });
        }

        return $this->postData;
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->postData);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->postData);
    }

    /**
     * @param array $keys
     * @return bool
     */
    public function hasAll(array $keys): bool
    {
        return empty(array_diff_key(array_flip($keys), $this->postData));
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isEmpty(string $key): bool
    {
        $value = $this->postData[$key] ?? null;
        return empty($value);
    }
}
