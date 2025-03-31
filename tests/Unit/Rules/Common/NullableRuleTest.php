<?php

namespace Tests\Unit\Rules\Common;

use PHPUnit\Framework\TestCase;
use stdClass;
use ValidatorPro\Rules\Common\NullableRule;

class NullableRuleTest extends TestCase
{
    /**
     * @var NullableRule
     */
    private NullableRule $rule;

    protected function setUp(): void
    {
        $this->rule = new NullableRule();
    }

    /**
     * @return array<array<mixed>>
     */
    public function validDataProvider(): array
    {
        return [
            [null],
            ['value'],
            [0],
            [false],
            [[]],
            [new stdClass()],
        ];
    }

    /**
     * @test
     * @dataProvider validDataProvider
     * @param mixed $value
     */
    public function it_validates_all_values($value): void
    {
        $this->assertTrue($this->rule->validate($value));
    }

    /**
     * @test
     */
    public function it_returns_correct_name(): void
    {
        $this->assertStringContainsString('nullable', $this->rule->getName());
    }

    /**
     * @test
     */
    public function it_returns_correct_message(): void
    {
        $this->assertStringContainsString('nullable', $this->rule->getMessage());
    }
}
