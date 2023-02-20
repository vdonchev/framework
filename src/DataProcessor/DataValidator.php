<?php

namespace Donchev\Framework\DataProcessor;

class DataValidator
{
    private const MISSING_ERROR_DEFAULT = 'Unknown Error';

    private const ERRORS = [
        'required' => 'Please enter the %s',
        'email' => 'The %s is not a valid email address',
        'min' => 'The %s must have at least %s characters',
        'max' => 'The %s must have at most %s characters',
        'between' => 'The %s must have between %d and %d characters',
        'integer' => 'The %s must be an integer',
        'numberMin' => 'The %s must be at least %s ot bigger',
        'numberMax' => 'The %s must be at most %s or bellow',
        'numberBetween' => 'The %s must be between %d and %d',
        'same' => 'The %s must match with %s',
        'alphanumeric' => 'The %s should have only letters and numbers',
        'secure' => 'The %s must have between 8 and 64 characters and contain at least one number, one upper case letter, one lower case letter and one special character',
        'unique' => 'The %s already exists',
        'ip' => 'The %s must be a valid IP address',
        'url' => 'The %s must be a valid IP address',
    ];

    private $globalErrors = [];

    private $fieldErrors = [];

    public function __construct()
    {
        $this->globalErrors = self::ERRORS;
    }

    public function addErrorMessages(array $messages)
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

                $callable = 'is' . ucfirst($rule);
                if (is_callable([$this, $callable])) {
                    if (!$this->$callable($data, $field, ...$parameters)) {
                        $error = $this->getError($field, $rule);
                        $errors[$field] = sprintf($error, $field, ...$parameters);
                    }
                }
            }
        }

        return $errors;
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

    public function isUrl(array $data, string $field): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return filter_var($data[$field], FILTER_VALIDATE_URL);
    }

    public function isIp(array $data, string $field): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return filter_var($data[$field], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
    }

    public function isNumber(array $data, string $field): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return filter_var($data[$field], FILTER_VALIDATE_INT);
    }

    public function isNumberMin(array $data, string $field, int $min): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return $data[$field] >= $min;
    }

    public function isNumberMax(array $data, string $field, int $max): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return $data[$field] <= $max;
    }

    public function isNumberBetween(array $data, string $field, int $min, int $max): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return $data[$field] >= $min && $data[$field] <= $max;
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

        return filter_var($data[$field], FILTER_VALIDATE_EMAIL);
    }

    public function isMin(array $data, string $field, int $min): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return mb_strlen($data[$field]) >= $min;
    }

    public function isMax(array $data, string $field, int $max): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        return mb_strlen($data[$field]) <= $max;
    }

    public function isBetween(array $data, string $field, int $min, int $max): bool
    {
        if (!isset($data[$field])) {
            return true;
        }

        $len = mb_strlen($data[$field]);
        return $len >= $min && $len <= $max;
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
        return preg_match($pattern, $data[$field]);
    }

    public function isUnique(array $data, string $field, string $table, string $column): bool
    {
        // todo
        return true;
    }
}
