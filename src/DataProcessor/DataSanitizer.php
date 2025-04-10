<?php

declare(strict_types=1);

namespace Donchev\Framework\DataProcessor;

use InvalidArgumentException;

class DataSanitizer
{
    /**
     * @param string|array $data
     * @param string $filter
     * @param bool $trim
     * @return mixed
     */
    public function sanitize(string|array $data, string $filter, bool $trim = false): mixed
    {
        $filter = self::getFilter($filter);

        if (is_array($data)) {
            if (array_product(array_map('is_scalar', $data)) === 0) {
                throw new InvalidArgumentException('Array values must be scalar');
            }

            $values = array_map([$this, $filter], $data);

            return $trim ? $this->trimArray($values) : $values;
        }

        $value = call_user_func([$this, $filter], $data);

        return $trim ? trim($value) : $value;
    }

    /**
     * @param string $string
     * @return string
     */
    public function sanitizeString(string $string): string
    {
        return strip_tags($string);
    }

    /**
     * @param string $url
     * @return string
     */
    public function sanitizeUrl(string $url): string
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * @param string $email
     * @return string
     */
    public function sanitizeEmail(string $email): string
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * @param string $number
     * @return int
     */
    public function sanitizeInt(string $number): int
    {
        return (int)filter_var($number, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * @param string $number
     * @return float
     */
    public function sanitizeFloat(string $number): float
    {
        return (float)filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * @param array $items
     * @return array
     */
    private function trimArray(array $items): array
    {
        return array_map(function ($item) {
            if (is_string($item)) {
                return trim($item);
            } elseif (is_array($item)) {
                return $this->trimArray($item);
            }

            return $item;
        }, $items);
    }

    /**
     * @param string $filter
     * @return string
     */
    private function getFilter(string $filter): string
    {
        return match ($filter) {
            'int' => 'sanitizeInt',
            'float' => 'sanitizeFloat',
            'email' => 'sanitizeEmail',
            'string' => 'sanitizeString',
            'url' => 'sanitizeUrl',
            default => throw new InvalidArgumentException('Invalid filter provided'),
        };
    }
}
