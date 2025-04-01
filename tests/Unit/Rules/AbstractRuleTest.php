<?php

namespace Tests\Unit\Rules;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Rules\AbstractRule;

class AbstractRuleTest extends TestCase
{
    /**
     * @var TestableAbstractRule
     */
    private TestableAbstractRule $rule;

    protected function setUp(): void
    {
        $this->rule = new TestableAbstractRule();
    }

    /**
     * @test
     * @dataProvider camelToSnakeProvider
     */
    public function it_correctly_converts_camel_case_to_snake_case(string $input, string $expected): void
    {
        $this->assertEquals($expected, $this->rule->testCamelToSnake($input));
    }

    /**
     * @test
     */
    public function it_returns_correct_name(): void
    {
        $this->assertEquals('testable', $this->rule->getName());
    }

    /**
     * @test
     */
    public function it_returns_default_message(): void
    {
        $this->assertNotEmpty($this->rule->getMessage());
    }

    /**
     * @test
     */
    public function it_can_set_and_get_parameters(): void
    {
        $params = ['key' => 'value', 'another' => 123];
        $this->rule->setParameters($params);
        $this->assertEquals($params, $this->rule->getParameters());
    }

    /**
     * @test
     */
    public function it_can_set_error_message(): void
    {
        $message = 'Custom error message';
        $this->rule->setErrorMessage($message);
        $this->assertEquals($message, $this->rule->getMessage());
    }

    /**
     * @test
     */
    public function it_returns_correct_documentation(): void
    {
        $documentation = $this->rule->getDocumentation();

        $this->assertArrayHasKey('name', $documentation);
        $this->assertArrayHasKey('description', $documentation);
        $this->assertArrayHasKey('parameters', $documentation);
        $this->assertArrayHasKey('examples', $documentation);

        $expectedDescription = 'Documentation for ' . $this->rule->getName() . ' rule';
        $this->assertSame($expectedDescription, $documentation['description']);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function camelToSnakeProvider(): array
    {
        return [
            ['camelCase', 'camel_case'],
            ['ThisIsATest', 'this_is_a_test'],
            ['', ''],
            ['simpleText', 'simple_text'],
        ];
    }

    /**
     * @test
     */
    public function it_supports_all_types_by_default(): void
    {
        // Test that the method returns true for any type
        $this->assertTrue($this->rule->supportsType('string'));
        $this->assertTrue($this->rule->supportsType('integer'));
        $this->assertTrue($this->rule->supportsType('boolean'));
        $this->assertTrue($this->rule->supportsType('any_other_type'));
    }
}

/**
 * Test class to expose protected methods for testing
 */
class TestableAbstractRule extends AbstractRule
{
    public function validate(mixed $value, array $parameters = [], array $data = []): bool
    {
        return true;
    }

    public function testCamelToSnake(string $input): string
    {
        return $this->camelToSnake($input);
    }

    public function getName(): string
    {
        return 'testable';
    }
}
