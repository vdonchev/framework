<?php

declare(strict_types=1);

namespace Tests\Http;

use Donchev\Framework\Http\Cookie;
use PHPUnit\Framework\TestCase;

class CookieTest extends TestCase
{
    public function testDayReturnsCorrectSeconds()
    {
        $this->assertEquals(86400, Cookie::day());
        $this->assertEquals(86400 * 3, Cookie::day(3));
    }

    public function testWeekReturnsCorrectSeconds()
    {
        $this->assertEquals(604800, Cookie::week());
        $this->assertEquals(604800 * 2, Cookie::week(2));
    }

    public function testMonthReturnsCorrectSeconds()
    {
        $this->assertEquals(2592000, Cookie::month());
        $this->assertEquals(2592000 * 4, Cookie::month(4));
    }

    public function testYearReturnsCorrectSeconds()
    {
        $this->assertEquals(31536000, Cookie::year());
        $this->assertEquals(31536000 * 2, Cookie::year(2));
    }

    public function testHasReturnsTrueWhenCookieExists(): void
    {
        $_COOKIE['test_cookie'] = 'value';
        self::assertTrue(Cookie::has('test_cookie'));
    }

    public function testHasReturnsFalseWhenCookieDoesNotExist(): void
    {
        unset($_COOKIE['missing_cookie']);
        self::assertFalse(Cookie::has('missing_cookie'));
    }

    public function testIsEmptyReturnsTrueForUnsetCookie(): void
    {
        unset($_COOKIE['unset_cookie']);
        self::assertTrue(Cookie::isEmpty('unset_cookie'));
    }

    public function testIsEmptyReturnsTrueForEmptyCookie(): void
    {
        $_COOKIE['empty_cookie'] = '';
        self::assertTrue(Cookie::isEmpty('empty_cookie'));
    }

    public function testIsEmptyReturnsFalseForNonEmptyCookie(): void
    {
        $_COOKIE['full_cookie'] = 'abc';
        self::assertFalse(Cookie::isEmpty('full_cookie'));
    }

    public function testGetReturnsValueWhenCookieExists(): void
    {
        $_COOKIE['my_cookie'] = 'my_value';
        self::assertSame('my_value', Cookie::get('my_cookie'));
    }

    public function testGetReturnsNullWhenCookieDoesNotExist(): void
    {
        unset($_COOKIE['ghost_cookie']);
        self::assertNull(Cookie::get('ghost_cookie'));
    }
}
