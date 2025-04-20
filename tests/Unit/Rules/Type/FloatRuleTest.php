<?php

namespace Tests\Unit\Rules\Type;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Rules\Type\FloatRule;

class FloatRuleTest extends TestCase
{
    private FloatRule $rule;

    protected function setUp(): void
    {
        $this->rule = new FloatRule();
    }

    /**
     * @return array<array<mixed>>
     */
    public function validFloatProvider(): array
    {
        return [
            [0.0],            // Zero float
            [1.0],            // Positive float
            [-1.0],           // Negative float
            [1.23e+10],       // Scientific notation
            [PHP_FLOAT_MIN],  // Minimum positive float
            [PHP_FLOAT_MAX],  // Maximum positive float
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function invalidFloatProvider(): array
    {
        return [
            [0],                  // Integer
            [1],                  // Integer
            ['1.23'],             // Numeric string
            ['test'],             // String
            [true],               // Boolean true
            [false],              // Boolean false
            [null],               // Null
            [[]],                 // Empty array
            [[1, 2, 3]],          // Array
            [new \stdClass()],    // Object
        ];
    }

    /**
     * @test
     * @dataProvider validFloatProvider
     * @param mixed $value
     */
    public function it_validates_float_values($value): void
    {
        $this->assertTrue($this->rule->validate($value));
    }

    /**
     * @test
     * @dataProvider invalidFloatProvider
     * @param mixed $value
     */
    public function it_rejects_non_float_values($value): void
    {
        $this->assertFalse($this->rule->validate($value));
    }

    /**
     * @test
     */
    public function it_returns_correct_message(): void
    {
        $this->assertStringContainsString('float', $this->rule->getMessage());
    }

    /**
     * @test
     */
    public function it_returns_correct_name(): void
    {
        $this->assertEquals('float', $this->rule->getName());
    }
}
