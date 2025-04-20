<?php

namespace Tests\Unit\Rules\Common;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Rules\Common\RequiredRule;

/**
 * Unit tests for the RequiredRule class.
 *
 * These tests verify that the RequiredRule correctly identifies
 * required values and rejects empty or null values.
 */
class RequiredRuleTest extends TestCase
{
    /**
     * The rule instance being tested.
     *
     * @var RequiredRule
     */
    private RequiredRule $rule;

    /**
     * Set up the test environment.
     *
     * Creates a fresh RequiredRule instance before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->rule = new RequiredRule();
    }

    /**
     * Provides valid data examples that should pass validation.
     *
     * @return array<array<mixed>> Array of test cases with valid data
     */
    public function validDataProvider(): array
    {
        return [
            ['value'],       // Non-empty string
            [0],             // Zero integer
            [false],         // Boolean false
            [[1, 2, 3]],     // Non-empty array
            [new \stdClass()], // Object
        ];
    }

    /**
     * Provides invalid data examples that should fail validation.
     *
     * @return array<array<mixed>> Array of test cases with invalid data
     */
    public function invalidDataProvider(): array
    {
        return [
            [null],          // Null value
            [''],            // Empty string
            [[]],            // Empty array
        ];
    }

    /**
     * Tests that valid values pass validation.
     *
     * @test
     * @dataProvider validDataProvider
     * @param mixed $value The value to test
     * @return void
     */
    public function it_validates_correct_values(mixed $value): void
    {
        $this->assertTrue($this->rule->validate($value));
    }

    /**
     * Tests that invalid values fail validation.
     *
     * @test
     * @dataProvider invalidDataProvider
     * @param mixed $value The value to test
     * @return void
     */
    public function it_rejects_invalid_values(mixed $value): void
    {
        $this->assertFalse($this->rule->validate($value));
    }

    /**
     * Tests that the rule name is correctly returned.
     *
     * @test
     * @return void
     */
    public function it_returns_correct_name(): void
    {
        $this->assertEquals('required', $this->rule->getName());
    }

    /**
     * Tests that the error message contains the expected text.
     *
     * @test
     * @return void
     */
    public function it_returns_correct_message(): void
    {
        $this->assertStringContainsString('required', $this->rule->getMessage());
    }

    /**
     * Tests that strings containing only whitespace are rejected.
     *
     * Verifies that the rule correctly identifies strings with spaces,
     * tabs, or newlines as empty and fails validation.
     *
     * @test
     * @return void
     */
    public function it_rejects_whitespace_only_strings(): void
    {
        $this->assertFalse($this->rule->validate('   '));
        $this->assertFalse($this->rule->validate("\t\n"));
    }
}
