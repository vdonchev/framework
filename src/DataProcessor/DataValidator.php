<?php

declare(strict_types=1);

namespace Donchev\Framework\DataProcessor;

class DataValidator
{
    private const MISSING_ERROR_DEFAULT = 'Unknown Error';

    private const ERRORS = [
        'required' => 'Required value is empty',
        'email' => '"%s" is not a valid email address',
        'min' => '"%s" must have at least %s characters',
        'max' => '"%s" must have at most %s characters',
        'between' => '"%s" must have between %d and %d characters',
        'number' => '"%s" must be an integer',
        'number_min' => '"%s" must be at least %d or bigger',
        'number_max' => '"%s" must be at most %d or bellow',
        'number_between' => '"%s" must be between %d and %d',
        'float' => '"%s" must be a floating point number',
        'float_min' => '"%s" must be at least %f or bigger',
        'float_max' => '"%s" must be at most %f or bellow',
        'float_between' => '"%s" must be between %f and %f',
        'same' => '"%s" must match with %s',
        'alphanumeric' => '"%s" should have only letters and numbers',
        'secure' => '"%s" must have between 8 and 64 characters and contain at least one number, one upper case letter, one lower case letter and one special character',
        'unique' => '"%s" already exists',
        'ip' => '"%s" must be a valid IP address',
        'url' => '"%s" must be a valid URL address',
    ];

    private array $globalErrors = [];

    private array $fieldErrors = [];

    public function __construct()
    {
        $this->globalErrors = self::ERRORS;
    }

    public function addErrorMessages(array $messages): void
    {
        foreach ($messages as $key => $message) {
            if (is_string($message)) {
                $this->globalErrors[$key] = $message;
            } else {
                if (is_array($message)) {
                    $this->fieldErrors[$key] = $message;
                }
            }
        }
    }

    public function validate(array $data, array $fields): array
    {
        $errors = [];

        foreach ($fields as $field => $option) {
            $rules = $this->split($option, '|');

            foreach ($rules as $ruleData) {
                $parameters = [];

                if (strpos($ruleData, ':')) {
                    [$rule, $params] = $this->split($ruleData, ':');
                    $parameters = $this->split($params, ',');
                } else {
                    $rule = trim($ruleData);
                }

                $methodName = $rule;
                if (preg_match_all('/_([a-z])/', $methodName, $matches)) {
                    for ($i = 0; $i < count($matches[0]); $i++) {
                        $methodName = str_replace($matches[0][$i], strtoupper($matches[1][$i]), $methodName);
                    }
                }

                $callable = 'is' . ucfirst($methodName);
                if (is_callable([$this, $callable])) {
                    if (!$this->$callable($data, $field, ...$parameters)) {
                        $error = $this->getError($field, $rule);
                        $errors[$field][] = sprintf($error, $field, ...$parameters);
                    }
                }
            }
        }

        return $errors;
    }

    public function isUrl(array $data, string $field): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return (bool)filter_var($data[$field], FILTER_VALIDATE_URL);
    }

    public function isIp(array $data, string $field): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return (bool)filter_var($data[$field], FILTER_VALIDATE_IP);
    }

    public function isNumber(array $data, string $field): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return (bool)filter_var($data[$field], FILTER_VALIDATE_INT);
    }

    public function isNumberMin(array $data, string $field, $min): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return $data[$field] >= (int)$min;
    }

    public function isNumberMax(array $data, string $field, $max): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return $data[$field] <= (int)$max;
    }

    public function isNumberBetween(array $data, string $field, $min, $max): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return $data[$field] >= (int)$min && $data[$field] <= (int)$max;
    }

    public function isFloat(array $data, string $field): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return (bool)filter_var($data[$field], FILTER_VALIDATE_FLOAT);
    }

    public function isFloatMin(array $data, string $field, $min): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return $data[$field] >= (float)$min;
    }

    public function isFloatMax(array $data, string $field, $max): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return $data[$field] <= (float)$max;
    }

    public function isFloatBetween(array $data, string $field, $min, $max): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return $data[$field] >= (float)$min && $data[$field] <= (float)$max;
    }

    public function isRequired(array $data, string $field): bool
    {
        return isset($data[$field]) && trim($data[$field]) !== '';
    }

    public function isEmail(array $data, string $field): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return (bool)filter_var($data[$field], FILTER_VALIDATE_EMAIL);
    }

    public function isMin(array $data, string $field, $min): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return mb_strlen($data[$field]) >= (int)$min;
    }

    public function isMax(array $data, string $field, $max): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return mb_strlen($data[$field]) <= (int)$max;
    }

    public function isBetween(array $data, string $field, $min, $max): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        $len = mb_strlen($data[$field]);
        return $len >= (int)$min && $len <= (int)$max;
    }

    public function isAlphanumeric(array $data, string $field): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return ctype_alnum($data[$field]);
    }

    public function isSame(array $data, string $field, string $other): bool
    {
        if (isset($data[$field], $data[$other])) {
            return $data[$field] === $data[$other];
        }

        if (!isset($data[$field]) && !isset($data[$other])) {
            return true;
        }

        return false;
    }

    public function isSecure(array $data, string $field): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        $pattern = "#.*^(?=.{8,64})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#";
        return (bool)preg_match($pattern, $data[$field]);
    }

    private function split(string $rule, string $separator): array
    {
        return array_map('trim', explode($separator, $rule));
    }

    private function getError(string $field, string $rule): string
    {
        if (array_key_exists($field, $this->fieldErrors)) {
            return $this->fieldErrors[$field][$rule];
        }

        if (array_key_exists($rule, $this->globalErrors)) {
            return $this->globalErrors[$rule];
        }

        return self::MISSING_ERROR_DEFAULT;
    }
}
