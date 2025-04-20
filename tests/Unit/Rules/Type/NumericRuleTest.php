<?php

namespace Tests\Unit\Rules\Type;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Rules\Type\NumericRule;

class NumericRuleTest extends TestCase
{
    private NumericRule $rule;

    protected function setUp(): void
    {
        $this->rule = new NumericRule();
    }

    /**
     * @return array<array<mixed>>
     */
    public function validNumericProvider(): array
    {
        return [
            [0],                // Integer 0
            [1],                // Positive integer
            [-1],               // Negative integer
            [0.0],              // Float 0
            [1.23],             // Positive float
            [-1.23],            // Negative float
            ['0'],              // String "0"
            ['1'],              // String "1"
            ['-1'],             // String "-1"
            ['1.23'],           // String "1.23"
            ['-1.23'],          // String "-1.23"
            ['0123'],           // Octal string
            ['1e10'],           // Scientific notation string
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function invalidNumericProvider(): array
    {
        return [
            ['test'],           // Non-numeric string
            ['123test'],        // Partially numeric string
            [''],               // Empty string
            [true],             // Boolean true
            [false],            // Boolean false
            [null],             // Null
            [[]],               // Empty array
            [[1, 2, 3]],        // Array
            [new \stdClass()],  // Object
        ];
    }

    /**
     * @test
     * @dataProvider validNumericProvider
     * @param mixed $value
     */
    public function it_validates_numeric_values($value): void
    {
        $this->assertTrue($this->rule->validate($value));
    }

    /**
     * @test
     * @dataProvider invalidNumericProvider
     * @param mixed $value
     */
    public function it_rejects_non_numeric_values($value): void
    {
        $this->assertFalse($this->rule->validate($value));
    }

    /**
     * @test
     */
    public function it_returns_correct_message(): void
    {
        $this->assertStringContainsString('number', $this->rule->getMessage());
    }

    /**
     * @test
     */
    public function it_returns_correct_name(): void
    {
        $this->assertEquals('numeric', $this->rule->getName());
    }
}
