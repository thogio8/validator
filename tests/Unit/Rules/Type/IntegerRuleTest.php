<?php

namespace Tests\Unit\Rules\Type;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Rules\Type\IntegerRule;

class IntegerRuleTest extends TestCase
{
    private IntegerRule $rule;

    protected function setUp(): void
    {
        $this->rule = new IntegerRule();
    }

    /**
     * @return array<array<mixed>>
     */
    public function validIntegerProvider(): array
    {
        return [
            [0],          // Zero
            [1],          // Positive integer
            [-1],         // Negative integer
            [PHP_INT_MAX], // Maximum integer
            [PHP_INT_MIN], // Minimum integer
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function invalidIntegerProvider(): array
    {
        return [
            ['123'],                // Numeric string
            ['test'],               // String
            [12.34],                // Float
            [true],                 // Boolean true
            [false],                // Boolean false
            [null],                 // Null
            [[]],                   // Empty array
            [[1, 2, 3]],            // Array
            [new \stdClass()],      // Object
        ];
    }

    /**
     * @test
     * @dataProvider validIntegerProvider
     * @param mixed $value
     */
    public function it_validates_integer_values($value): void
    {
        $this->assertTrue($this->rule->validate($value));
    }

    /**
     * @test
     * @dataProvider invalidIntegerProvider
     * @param mixed $value
     */
    public function it_rejects_non_integer_values($value): void
    {
        $this->assertFalse($this->rule->validate($value));
    }

    /**
     * @test
     */
    public function it_returns_correct_message(): void
    {
        $this->assertStringContainsString('integer', $this->rule->getMessage());
    }

    /**
     * @test
     */
    public function it_returns_correct_name(): void
    {
        $this->assertEquals('integer', $this->rule->getName());
    }
}
