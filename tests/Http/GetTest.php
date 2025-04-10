<?php

declare(strict_types=1);

namespace Tests\Http;

use Donchev\Framework\Http\Get;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class GetTest extends TestCase
{
    private function createGetInstanceWithData(array $data): Get
    {
        $get = new Get();

        // Use reflection to set private property $getData
        $reflection = new ReflectionClass(Get::class);
        $property = $reflection->getProperty('getData');
        $property->setAccessible(true);
        $property->setValue($get, $data);

        return $get;
    }

    public function testGetReturnsValueAndTrims(): void
    {
        $get = $this->createGetInstanceWithData(['name' => '  John  ']);
        self::assertSame('John', $get->get('name'));
    }

    public function testGetReturnsRawWhenNoTrim(): void
    {
        $get = $this->createGetInstanceWithData(['name' => '  John  ']);
        self::assertSame('  John  ', $get->get('name', false));
    }

    public function testGetReturnsNullIfMissing(): void
    {
        $get = $this->createGetInstanceWithData([]);
        self::assertNull($get->get('unknown'));
    }

    public function testGetAllTrimsValues(): void
    {
        $get = $this->createGetInstanceWithData(['a' => ' 1 ', 'b' => [' 2 ', ' 3 ']]);
        $expected = ['a' => '1', 'b' => ['2', '3']];
        self::assertSame($expected, $get->getAll(true));
    }

    public function testGetAllReturnsRaw(): void
    {
        $input = ['a' => ' 1 ', 'b' => [' 2 ', ' 3 ']];
        $get = $this->createGetInstanceWithData($input);
        self::assertSame($input, $get->getAll(false));
    }

    public function testKeysReturnsAllKeys(): void
    {
        $get = $this->createGetInstanceWithData(['a' => 1, 'b' => 2]);
        self::assertSame(['a', 'b'], $get->keys());
    }

    public function testHasReturnsTrueIfExists(): void
    {
        $get = $this->createGetInstanceWithData(['key' => 'val']);
        self::assertTrue($get->has('key'));
    }

    public function testHasReturnsFalseIfMissing(): void
    {
        $get = $this->createGetInstanceWithData([]);
        self::assertFalse($get->has('missing'));
    }

    public function testHasAllReturnsTrueIfAllExist(): void
    {
        $get = $this->createGetInstanceWithData(['a' => 1, 'b' => 2]);
        self::assertTrue($get->hasAll(['a', 'b']));
    }

    public function testHasAllReturnsFalseIfSomeMissing(): void
    {
        $get = $this->createGetInstanceWithData(['a' => 1]);
        self::assertFalse($get->hasAll(['a', 'b']));
    }

    public function testIsEmptyReturnsTrueForMissingOrEmpty(): void
    {
        $get = $this->createGetInstanceWithData(['a' => '', 'b' => null]);
        self::assertTrue($get->isEmpty('a'));
        self::assertTrue($get->isEmpty('b'));
        self::assertTrue($get->isEmpty('missing'));
    }

    public function testIsEmptyReturnsFalseForNonEmpty(): void
    {
        $get = $this->createGetInstanceWithData(['a' => 'hello', 'b' => 123]);
        self::assertFalse($get->isEmpty('a'));
        self::assertFalse($get->isEmpty('b'));
    }
}
