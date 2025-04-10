<?php

declare(strict_types=1);

namespace Donchev\Framework\Security;

use Exception;

class TokenGenerator
{
    /**
     * @throws Exception
     */
    public static function generate(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}
