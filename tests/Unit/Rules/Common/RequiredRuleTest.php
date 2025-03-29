<?php

namespace Tests\Unit\Rules\Common;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Rules\Common\RequiredRule;

class RequiredRuleTest extends TestCase
{
    /**
     * @var RequiredRule
     */
    private RequiredRule $rule;
    
    protected function setUp(): void
    {
        $this->rule = new RequiredRule();
    }
    
    /**
     * @return array<array<mixed>>
     */
    public function validDataProvider(): array
    {
        return [
            ['value'],
            [0],
            [false],
            [[1, 2, 3]],
            [new \stdClass()],
        ];
    }
    
    /**
     * @return array<array<mixed>>
     */
    public function invalidDataProvider(): array
    {
        return [
            [null],
            [''],
            [[]],
        ];
    }
    
    /**
     * @test
     * @dataProvider validDataProvider
     * @param mixed $value
     */
    public function it_validates_correct_values($value): void
    {
        $this->assertTrue($this->rule->validate($value));
    }
    
    /**
     * @test
     * @dataProvider invalidDataProvider
     * @param mixed $value
     */
    public function it_rejects_invalid_values($value): void
    {
        $this->assertFalse($this->rule->validate($value));
    }
    
    /**
     * @test
     */
    public function it_returns_correct_name(): void
    {
        $this->assertEquals('required', $this->rule->getName());
    }
    
    /**
     * @test
     */
    public function it_returns_correct_message(): void
    {
        $this->assertStringContainsString('obligatoire', $this->rule->getMessage());
    }
}
