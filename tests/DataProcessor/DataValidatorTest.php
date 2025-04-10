<?php

declare(strict_types=1);

namespace Tests\DataProcessor;

use Donchev\Framework\DataProcessor\DataValidator;
use PHPUnit\Framework\TestCase;

class DataValidatorTest extends TestCase
{
    private DataValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new DataValidator();
    }

    public function testRequired()
    {
        $errors = $this->validator->validate(['name' => ''], ['name' => 'required']);
        $this->assertArrayHasKey('name', $errors);
    }

    public function testEmail()
    {
        $errors = $this->validator->validate(['email' => 'invalid'], ['email' => 'email']);
        $this->assertArrayHasKey('email', $errors);
    }

    public function testMin()
    {
        $errors = $this->validator->validate(['name' => 'ab'], ['name' => 'min:3']);
        $this->assertArrayHasKey('name', $errors);
    }

    public function testMax()
    {
        $errors = $this->validator->validate(['name' => 'abcdef'], ['name' => 'max:5']);
        $this->assertArrayHasKey('name', $errors);
    }

    public function testBetween()
    {
        $errors = $this->validator->validate(['name' => 'a'], ['name' => 'between:2,5']);
        $this->assertArrayHasKey('name', $errors);
    }

    public function testNumber()
    {
        $errors = $this->validator->validate(['age' => 'abc'], ['age' => 'number']);
        $this->assertArrayHasKey('age', $errors);
    }

    public function testNumberMin()
    {
        $errors = $this->validator->validate(['age' => 3], ['age' => 'number_min:5']);
        $this->assertArrayHasKey('age', $errors);
    }

    public function testNumberMax()
    {
        $errors = $this->validator->validate(['age' => 10], ['age' => 'number_max:5']);
        $this->assertArrayHasKey('age', $errors);
    }

    public function testNumberBetween()
    {
        $errors = $this->validator->validate(['age' => 2], ['age' => 'number_between:3,5']);
        $this->assertArrayHasKey('age', $errors);
    }

    public function testFloat()
    {
        $errors = $this->validator->validate(['score' => 'abc'], ['score' => 'float']);
        $this->assertArrayHasKey('score', $errors);
    }

    public function testFloatMin()
    {
        $errors = $this->validator->validate(['score' => 1.2], ['score' => 'float_min:2.5']);
        $this->assertArrayHasKey('score', $errors);
    }

    public function testFloatMax()
    {
        $errors = $this->validator->validate(['score' => 9.9], ['score' => 'float_max:5.5']);
        $this->assertArrayHasKey('score', $errors);
    }

    public function testFloatBetween()
    {
        $errors = $this->validator->validate(['score' => 1.2], ['score' => 'float_between:2.0,5.0']);
        $this->assertArrayHasKey('score', $errors);
    }

    public function testSame()
    {
        $data = ['password' => 'abc123', 'confirm' => 'abc321'];
        $errors = $this->validator->validate($data, ['confirm' => 'same:password']);
        $this->assertArrayHasKey('confirm', $errors);
    }

    public function testAlphanumeric()
    {
        $errors = $this->validator->validate(['user' => 'abc@123'], ['user' => 'alphanumeric']);
        $this->assertArrayHasKey('user', $errors);
    }

    public function testSecurePassword()
    {
        $errors = $this->validator->validate(['pass' => '123456'], ['pass' => 'secure']);
        $this->assertArrayHasKey('pass', $errors);
    }

    public function testIp()
    {
        $errors = $this->validator->validate(['ip' => 'invalid-ip'], ['ip' => 'ip']);
        $this->assertArrayHasKey('ip', $errors);
    }

    public function testUrl()
    {
        $errors = $this->validator->validate(['url' => 'notaurl'], ['url' => 'url']);
        $this->assertArrayHasKey('url', $errors);
    }

    public function testRequiredPassesWithNonEmptyValue()
    {
        $errors = $this->validator->validate(['name' => 'Ivan'], ['name' => 'required']);
        $this->assertEmpty($errors);
    }

    public function testEmailPassesWithValidEmail()
    {
        $errors = $this->validator->validate(['email' => 'ivan@example.com'], ['email' => 'email']);
        $this->assertEmpty($errors);
    }

    public function testMinPassesWithEnoughCharacters()
    {
        $errors = $this->validator->validate(['name' => 'abcd'], ['name' => 'min:3']);
        $this->assertEmpty($errors);
    }

    public function testMaxPassesWithFewEnoughCharacters()
    {
        $errors = $this->validator->validate(['name' => 'abc'], ['name' => 'max:5']);
        $this->assertEmpty($errors);
    }

    public function testBetweenPassesWithinRange()
    {
        $errors = $this->validator->validate(['name' => 'abcd'], ['name' => 'between:2,5']);
        $this->assertEmpty($errors);
    }

    public function testNumberPassesWithInteger()
    {
        $errors = $this->validator->validate(['age' => 30], ['age' => 'number']);
        $this->assertEmpty($errors);
    }

    public function testNumberMinPasses()
    {
        $errors = $this->validator->validate(['age' => 10], ['age' => 'number_min:5']);
        $this->assertEmpty($errors);
    }

    public function testNumberMaxPasses()
    {
        $errors = $this->validator->validate(['age' => 3], ['age' => 'number_max:5']);
        $this->assertEmpty($errors);
    }

    public function testNumberBetweenPasses()
    {
        $errors = $this->validator->validate(['age' => 4], ['age' => 'number_between:3,5']);
        $this->assertEmpty($errors);
    }

    public function testFloatPassesWithValidValue()
    {
        $errors = $this->validator->validate(['score' => 3.14], ['score' => 'float']);
        $this->assertEmpty($errors);
    }

    public function testFloatMinPasses()
    {
        $errors = $this->validator->validate(['score' => 5.6], ['score' => 'float_min:2.5']);
        $this->assertEmpty($errors);
    }

    public function testFloatMaxPasses()
    {
        $errors = $this->validator->validate(['score' => 3.2], ['score' => 'float_max:5.5']);
        $this->assertEmpty($errors);
    }

    public function testFloatBetweenPasses()
    {
        $errors = $this->validator->validate(['score' => 3.5], ['score' => 'float_between:2.0,5.0']);
        $this->assertEmpty($errors);
    }

    public function testSamePassesWhenMatching()
    {
        $data = ['password' => 'abc123', 'confirm' => 'abc123'];
        $errors = $this->validator->validate($data, ['confirm' => 'same:password']);
        $this->assertEmpty($errors);
    }

    public function testAlphanumericPasses()
    {
        $errors = $this->validator->validate(['user' => 'abc123'], ['user' => 'alphanumeric']);
        $this->assertEmpty($errors);
    }

    public function testSecurePasswordPasses()
    {
        $errors = $this->validator->validate(['pass' => 'StrongPass1@'], ['pass' => 'secure']);
        $this->assertEmpty($errors);
    }

    public function testIpPasses()
    {
        $errors = $this->validator->validate(['ip' => '192.168.1.1'], ['ip' => 'ip']);
        $this->assertEmpty($errors);
    }

    public function testUrlPasses()
    {
        $errors = $this->validator->validate(['url' => 'https://example.com'], ['url' => 'url']);
        $this->assertEmpty($errors);
    }

    public function testCustomErrorMessagesPerFieldOverridesGlobal()
    {
        $this->validator->addErrorMessages([
            'email' => [
                'email' => 'Невалиден имейл адрес в полето "email"',
            ],
        ]);

        $data = ['email' => 'not-an-email'];
        $rules = ['email' => 'required|email'];

        $errors = $this->validator->validate($data, $rules);
        $this->assertEquals('Невалиден имейл адрес в полето "email"', $errors['email'][0]);
    }

    public function testAddErrorMessagesWithInvalidKeysDoesNotCrash()
    {
        $this->validator->addErrorMessages([
            123 => ['invalid'],
            'number' => null,
        ]);

        $data = ['email' => 'bad'];
        $rules = ['email' => 'email'];

        $errors = $this->validator->validate($data, $rules);

        $this->assertArrayHasKey('email', $errors);
    }
}
