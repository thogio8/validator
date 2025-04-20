<?php

namespace Tests\Unit\Rules\Type;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Rules\Type\BooleanRule;

class BooleanRuleTest extends TestCase
{
    private BooleanRule $rule;

    protected function setUp(): void
    {
        $this->rule = new BooleanRule();
    }

    /**
     * @return array<array<mixed>>
     */
    public function validBooleanProvider(): array
    {
        return [
            [true],   // Boolean true
            [false],  // Boolean false
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function invalidBooleanProvider(): array
    {
        return [
            ['true'],                // String "true"
            ['false'],               // String "false"
            [0],                     // Integer 0
            [1],                     // Integer 1
            ['0'],                   // String "0"
            ['1'],                   // String "1"
            [''],                    // Empty string
            [12.34],                 // Float
            [null],                  // Null
            [[]],                    // Empty array
            [[1, 2, 3]],             // Array
            [new \stdClass()],       // Object
        ];
    }

    /**
     * @test
     * @dataProvider validBooleanProvider
     * @param mixed $value
     */
    public function it_validates_boolean_values($value): void
    {
        $this->assertTrue($this->rule->validate($value));
    }

    /**
     * @test
     * @dataProvider invalidBooleanProvider
     * @param mixed $value
     */
    public function it_rejects_non_boolean_values($value): void
    {
        $this->assertFalse($this->rule->validate($value));
    }

    /**
     * @test
     */
    public function it_returns_correct_message(): void
    {
        $this->assertStringContainsString('boolean', $this->rule->getMessage());
    }

    /**
     * @test
     */
    public function it_returns_correct_name(): void
    {
        $this->assertEquals('boolean', $this->rule->getName());
    }
}
