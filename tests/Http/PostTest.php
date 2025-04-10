<?php

declare(strict_types=1);

namespace Tests\Http;

use Donchev\Framework\Http\Post;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class PostTest extends TestCase
{
    private function createPostInstanceWithData(array $data): Post
    {
        $post = new Post();

        // Use reflection to set private property $postData
        $reflection = new ReflectionClass(Post::class);
        $property = $reflection->getProperty('postData');
        $property->setAccessible(true);
        $property->setValue($post, $data);

        return $post;
    }

    public function testGetReturnsValueAndTrims(): void
    {
        $post = $this->createPostInstanceWithData(['name' => '  John  ']);
        self::assertSame('John', $post->get('name'));
    }

    public function testGetReturnsRawWhenNoTrim(): void
    {
        $post = $this->createPostInstanceWithData(['name' => '  John  ']);
        self::assertSame('  John  ', $post->get('name', false));
    }

    public function testGetReturnsNullIfMissing(): void
    {
        $post = $this->createPostInstanceWithData([]);
        self::assertNull($post->get('unknown'));
    }

    public function testGetAllTrimsValues(): void
    {
        $post = $this->createPostInstanceWithData(['a' => ' 1 ', 'b' => [' 2 ', ' 3 ']]);
        $expected = ['a' => '1', 'b' => ['2', '3']];
        self::assertSame($expected, $post->getAll(true));
    }

    public function testGetAllReturnsRaw(): void
    {
        $input = ['a' => ' 1 ', 'b' => [' 2 ', ' 3 ']];
        $post = $this->createPostInstanceWithData($input);
        self::assertSame($input, $post->getAll(false));
    }

    public function testKeysReturnsAllKeys(): void
    {
        $post = $this->createPostInstanceWithData(['a' => 1, 'b' => 2]);
        self::assertSame(['a', 'b'], $post->keys());
    }

    public function testHasReturnsTrueIfExists(): void
    {
        $post = $this->createPostInstanceWithData(['key' => 'val']);
        self::assertTrue($post->has('key'));
    }

    public function testHasReturnsFalseIfMissing(): void
    {
        $post = $this->createPostInstanceWithData([]);
        self::assertFalse($post->has('missing'));
    }

    public function testHasAllReturnsTrueIfAllExist(): void
    {
        $post = $this->createPostInstanceWithData(['a' => 1, 'b' => 2]);
        self::assertTrue($post->hasAll(['a', 'b']));
    }

    public function testHasAllReturnsFalseIfSomeMissing(): void
    {
        $post = $this->createPostInstanceWithData(['a' => 1]);
        self::assertFalse($post->hasAll(['a', 'b']));
    }

    public function testIsEmptyReturnsTrueForMissingOrEmpty(): void
    {
        $post = $this->createPostInstanceWithData(['a' => '', 'b' => null]);
        self::assertTrue($post->isEmpty('a'));
        self::assertTrue($post->isEmpty('b'));
        self::assertTrue($post->isEmpty('missing'));
    }

    public function testIsEmptyReturnsFalseForNonEmpty(): void
    {
        $post = $this->createPostInstanceWithData(['a' => 'hello', 'b' => 123]);
        self::assertFalse($post->isEmpty('a'));
        self::assertFalse($post->isEmpty('b'));
    }
}
