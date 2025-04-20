<?php

namespace Tests\Unit\Rules\Type;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Rules\Type\StringRule;

class StringRuleTest extends TestCase
{
    private StringRule $rule;

    protected function setUp(): void
    {
        $this->rule = new StringRule();
    }

    /**
     * @return array<array<mixed>>
     */
    public function validStringProvider(): array
    {
        return [
            [''],                    // Empty string
            ['test'],                // Regular string
            ['123'],                 // Numeric string
            ['null'],                // String "null"
            ['true'],                // String "true"
            ['false'],               // String "false"
            ["multi\nline\nstring"], // Multiline string
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function invalidStringProvider(): array
    {
        return [
            [123],                  // Integer
            [12.34],                // Float
            [true],                 // Boolean true
            [false],                // Boolean false
            [null],                 // Null
            [[]],                   // Empty array
            [[1, 2, 3]],            // Numeric array
            [['key' => 'value']],   // Associative array
            [new \stdClass()],      // Object
        ];
    }

    /**
     * @test
     * @dataProvider validStringProvider
     * @param mixed $value
     */
    public function it_validates_string_values($value): void
    {
        $this->assertTrue($this->rule->validate($value));
    }

    /**
     * @test
     * @dataProvider invalidStringProvider
     * @param mixed $value
     */
    public function it_rejects_non_string_values($value): void
    {
        $this->assertFalse($this->rule->validate($value));
    }

    /**
     * @test
     */
    public function it_returns_correct_message(): void
    {
        $this->assertStringContainsString('string', $this->rule->getMessage());
    }

    /**
     * @test
     */
    public function it_returns_correct_name(): void
    {
        $this->assertEquals('string', $this->rule->getName());
    }
}
