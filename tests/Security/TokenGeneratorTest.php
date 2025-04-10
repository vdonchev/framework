<?php

declare(strict_types=1);

namespace Tests\Security;

use Donchev\Framework\Security\TokenGenerator;
use PHPUnit\Framework\TestCase;
use ValueError;

final class TokenGeneratorTest extends TestCase
{
    public function testGeneratesCorrectLength(): void
    {
        $length = 16;
        $token = TokenGenerator::generate($length);
        self::assertSame($length * 2, strlen($token));
    }

    public function testGeneratesDifferentTokens(): void
    {
        $token1 = TokenGenerator::generate(16);
        $token2 = TokenGenerator::generate(16);
        self::assertNotSame($token1, $token2);
    }

    public function testThrowsExceptionOnNegativeLength(): void
    {
        $this->expectException(ValueError::class);
        TokenGenerator::generate(-5);
    }
}
