<?php

namespace Tests\Unit\Rules\Type;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Rules\Type\ObjectRule;

class ObjectRuleTest extends TestCase
{
    private ObjectRule $rule;

    protected function setUp(): void
    {
        $this->rule = new ObjectRule();
    }

    /**
     * @return array<array<mixed>>
     */
    public function validObjectProvider(): array
    {
        return [
            [new \stdClass()],                      // Standard object
            [(object) ['property' => 'value']],     // Cast array to object
            [new class () {}],                        // Anonymous class
            [$this],                                // Test case object
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function invalidObjectProvider(): array
    {
        return [
            ['object'],              // String
            [123],                   // Integer
            [12.34],                 // Float
            [true],                  // Boolean true
            [false],                 // Boolean false
            [null],                  // Null
            [[]],                    // Empty array
            [[1, 2, 3]],             // Numeric array
            [['key' => 'value']],    // Associative array
        ];
    }

    /**
     * @test
     * @dataProvider validObjectProvider
     * @param mixed $value
     */
    public function it_validates_object_values($value): void
    {
        $this->assertTrue($this->rule->validate($value));
    }

    /**
     * @test
     * @dataProvider invalidObjectProvider
     * @param mixed $value
     */
    public function it_rejects_non_object_values($value): void
    {
        $this->assertFalse($this->rule->validate($value));
    }

    /**
     * @test
     */
    public function it_returns_correct_message(): void
    {
        $this->assertStringContainsString('object', $this->rule->getMessage());
    }

    /**
     * @test
     */
    public function it_returns_correct_name(): void
    {
        $this->assertEquals('object', $this->rule->getName());
    }
}
