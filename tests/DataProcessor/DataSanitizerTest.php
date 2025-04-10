<?php

declare(strict_types=1);

namespace Tests\DataProcessor;

use Donchev\Framework\DataProcessor\DataSanitizer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DataSanitizerTest extends TestCase
{
    private DataSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new DataSanitizer();
    }

    public function testSanitizeString(): void
    {
        $input = '<h1>Hello World</h1>';
        $sanitized = $this->sanitizer->sanitizeString($input);
        $this->assertSame('Hello World', $sanitized);
    }

    public function testSanitizeUrl(): void
    {
        $input = 'http://example.com';
        $sanitized = $this->sanitizer->sanitizeUrl($input);
        $this->assertSame('http://example.com', $sanitized);
    }

    public function testSanitizeEmail(): void
    {
        $input = 'test@domain.com';
        $sanitized = $this->sanitizer->sanitizeEmail($input);
        $this->assertSame('test@domain.com', $sanitized);
    }

    public function testSanitizeInt(): void
    {
        $input = '123abc';
        $sanitized = $this->sanitizer->sanitizeInt($input);
        $this->assertSame(123, $sanitized);
    }

    public function testSanitizeFloat(): void
    {
        $input = '123.45abc';
        $sanitized = $this->sanitizer->sanitizeFloat($input);
        $this->assertSame(123.45, $sanitized);
    }

    public function testSanitizeArrayWithValidValues(): void
    {
        $input = ['<h1>Test</h1>', 'http://example.com'];
        $sanitized = $this->sanitizer->sanitize($input, 'string');
        $this->assertSame(['Test', 'http://example.com'], $sanitized);
    }

    public function testSanitizeArrayWithInvalidValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Array values must be scalar');

        $input = ['<h1>Test</h1>', ['nested']];
        $this->sanitizer->sanitize($input, 'string');
    }

    public function testSanitizeArrayWithTrim(): void
    {
        $input = ['  Test  ', '  Example  '];
        $sanitized = $this->sanitizer->sanitize($input, 'string', true);
        $this->assertSame(['Test', 'Example'], $sanitized);
    }

    public function testSanitizeArrayWithInvalidFilter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid filter provided');

        $this->sanitizer->sanitize('Test', 'invalidFilter');
    }

    public function testSanitizeStringArray(): void
    {
        $input = ['<h1>Test</h1>', 'http://example.com'];
        $sanitized = $this->sanitizer->sanitize($input, 'string');
        $this->assertSame(['Test', 'http://example.com'], $sanitized);
    }

    public function testSanitizeArrayWithMixedTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Array values must be scalar');

        $input = ['<h1>Test</h1>', ['nested'], 'http://example.com'];
        $this->sanitizer->sanitize($input, 'string');
    }
}
