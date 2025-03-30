<?php

namespace Tests\Unit\Rules\String;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Rules\String\EmailRule;

class EmailRuleTest extends TestCase
{
    /**
     * @var EmailRule
     */
    private EmailRule $rule;

    protected function setUp(): void
    {
        $this->rule = new EmailRule();
    }

    /**
     * @return array<array<mixed>>
     */
    public function validEmailsProvider(): array
    {
        return [
            ['test@example.com'],
            ['test.user@example.com'],
            ['test+user@example.com'],
            ['test@subdomain.example.com'],
            ['123@example.com'],
            ['test@example.co.uk'],
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function invalidEmailsProvider(): array
    {
        return [
            [''],                     // Empty string
            ['test'],                 // No @ symbol
            ['test@'],                // No domain
            ['@example.com'],         // No local part
            ['test@example'],         // Incomplete domain
            ['test@.com'],            // Domain starts with dot
            ['test@example..com'],    // Consecutive dots
            [123],                    // Not a string
            [null],                   // Null
            [[]],                     // Array
            [new \stdClass()],        // Object
        ];
    }

    /**
     * @test
     * @dataProvider validEmailsProvider
     * @param mixed $email
     */
    public function it_validates_correct_emails($email): void
    {
        $this->assertTrue($this->rule->validate($email));
    }

    /**
     * @test
     * @dataProvider invalidEmailsProvider
     * @param mixed $email
     */
    public function it_rejects_invalid_emails($email): void
    {
        $this->assertFalse($this->rule->validate($email));
    }

    /**
     * @test
     */
    public function it_returns_correct_name(): void
    {
        $this->assertEquals('email', $this->rule->getName());
    }

    /**
     * @test
     */
    public function it_returns_correct_message(): void
    {
        $this->assertStringContainsString('email', $this->rule->getMessage());
    }
}
