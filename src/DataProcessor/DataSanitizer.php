<?php

namespace Donchev\Framework\DataProcessor;

class DataSanitizer
{
    private const DEFAULT_FILTER = FILTER_SANITIZE_STRING;

    private const FILTERS = [
        'string' => FILTER_SANITIZE_STRING,
        'string[]' => [
            'filter' => FILTER_SANITIZE_STRING,
            'flags' => FILTER_REQUIRE_ARRAY
        ],
        'email' => FILTER_SANITIZE_EMAIL,
        'int' => [
            'filter' => FILTER_SANITIZE_NUMBER_INT,
            'flags' => FILTER_REQUIRE_SCALAR
        ],
        'int[]' => [
            'filter' => FILTER_SANITIZE_NUMBER_INT,
            'flags' => FILTER_REQUIRE_ARRAY
        ],
        'float' => [
            'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
            'flags' => FILTER_FLAG_ALLOW_FRACTION
        ],
        'float[]' => [
            'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
            'flags' => FILTER_REQUIRE_ARRAY
        ],
        'url' => FILTER_SANITIZE_URL,
    ];

    public function sanitize(
        $input,
        $type = null,
        int $defaultFilter = self::DEFAULT_FILTER,
        bool $trim = true
    ) {
        if ($type) {
            $options = self::FILTERS[$type];
            $data = filter_var($input, $options);
        } else {
            $data = filter_var($input, $defaultFilter);
        }

        return $trim ? trim($data) : $data;
    }

    public function sanitizeArray(
        array $inputs,
        array $fields = [],
        int $defaultFilter = self::DEFAULT_FILTER,
        bool $trim = true
    ): array {
        if ($fields) {
            $options = $this->buildOptions($fields);
            $data = filter_var_array($inputs, $options);
        } else {
            $data = filter_var_array($inputs, $defaultFilter);
        }

        return $trim ? $this->trimArray($data) : $data;
    }

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

    private function buildOptions(array $fields): array
    {
        $options = [];
        foreach ($fields as $key => $field) {
            $options[$key] = self::FILTERS[$field];
        }

        return $options;
    }
}
