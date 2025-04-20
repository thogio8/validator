<?php

namespace Tests\Unit\Rules\Type;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Rules\Type\ArrayRule;

class ArrayRuleTest extends TestCase
{
    private ArrayRule $rule;

    protected function setUp(): void
    {
        $this->rule = new ArrayRule();
    }

    /**
     * @return array<array<mixed>>
     */
    public function validArrayProvider(): array
    {
        return [
            [[]],                    // Empty array
            [[1, 2, 3]],             // Numeric array
            [['key' => 'value']],    // Associative array
            [['mixed', 2, true]],    // Mixed array
            [['nested' => [1, 2]]],  // Nested array
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function invalidArrayProvider(): array
    {
        return [
            ['array'],               // String
            [123],                   // Integer
            [12.34],                 // Float
            [true],                  // Boolean true
            [false],                 // Boolean false
            [null],                  // Null
            [new \stdClass()],       // Object
        ];
    }

    /**
     * @test
     * @dataProvider validArrayProvider
     * @param mixed $value
     */
    public function it_validates_array_values($value): void
    {
        $this->assertTrue($this->rule->validate($value));
    }

    /**
     * @test
     * @dataProvider invalidArrayProvider
     * @param mixed $value
     */
    public function it_rejects_non_array_values($value): void
    {
        $this->assertFalse($this->rule->validate($value));
    }

    /**
     * @test
     */
    public function it_returns_correct_message(): void
    {
        $this->assertStringContainsString('array', $this->rule->getMessage());
    }

    /**
     * @test
     */
    public function it_returns_correct_name(): void
    {
        $this->assertEquals('array', $this->rule->getName());
    }
}
