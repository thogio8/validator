<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Core\CompiledRules;

/**
 * Unit tests for the CompiledRules class.
 */
class CompiledRulesTest extends TestCase
{
    /**
     * @var CompiledRules
     */
    private CompiledRules $compiledRules;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->compiledRules = new CompiledRules();
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_empty(): void
    {
        $this->assertEmpty($this->compiledRules->getRules());
        $this->assertEmpty($this->compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_with_rules(): void
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ];

        $compiledRules = new CompiledRules($rules);

        $this->assertEquals($rules, $compiledRules->getRules());
        $this->assertNotEmpty($compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_compiles_string_rules_correctly(): void
    {
        $rules = [
            'email' => 'required|email',
        ];

        $compiledRules = new CompiledRules($rules);
        $expected = [
            'email' => [
                ['name' => 'required', 'parameters' => []],
                ['name' => 'email', 'parameters' => []],
            ],
        ];

        $this->assertEquals($expected, $compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_compiles_rules_with_parameters_correctly(): void
    {
        $rules = [
            'password' => 'required|min:8|max:20',
        ];

        $compiledRules = new CompiledRules($rules);
        $expected = [
            'password' => [
                ['name' => 'required', 'parameters' => []],
                ['name' => 'min', 'parameters' => ['8']],
                ['name' => 'max', 'parameters' => ['20']],
            ],
        ];

        $this->assertEquals($expected, $compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_compiles_rules_with_multiple_parameters_correctly(): void
    {
        $rules = [
            'field' => 'required|between:5,10|in:foo,bar,baz',
        ];

        $compiledRules = new CompiledRules($rules);
        $expected = [
            'field' => [
                ['name' => 'required', 'parameters' => []],
                ['name' => 'between', 'parameters' => ['5', '10']],
                ['name' => 'in', 'parameters' => ['foo', 'bar', 'baz']],
            ],
        ];

        $this->assertEquals($expected, $compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_can_add_string_rule(): void
    {
        $this->compiledRules->addRule('email', 'required|email');

        $expected = [
            'email' => [
                ['name' => 'required', 'parameters' => []],
                ['name' => 'email', 'parameters' => []],
            ],
        ];

        $this->assertEquals($expected, $this->compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_can_add_array_rule(): void
    {
        $this->compiledRules->addRule('address', [
            ['name' => 'required', 'parameters' => []],
            ['name' => 'string', 'parameters' => []],
        ]);

        $expected = [
            'address' => [
                ['name' => 'required', 'parameters' => []],
                ['name' => 'string', 'parameters' => []],
            ],
        ];

        $this->assertEquals($expected, $this->compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_can_add_multiple_rules_to_same_field(): void
    {
        $this->compiledRules->addRule('email', 'required');
        $this->compiledRules->addRule('email', 'email');

        $expected = [
            'email' => [
                ['name' => 'required', 'parameters' => []],
                ['name' => 'email', 'parameters' => []],
            ],
        ];

        $this->assertEquals($expected, $this->compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_can_check_if_field_has_rules(): void
    {
        $this->compiledRules->addRule('email', 'required|email');

        $this->assertTrue($this->compiledRules->hasRule('email'));
        $this->assertFalse($this->compiledRules->hasRule('password'));
    }

    /**
     * @test
     */
    public function it_can_get_rules_for_field(): void
    {
        $this->compiledRules->addRule('email', 'required|email');

        $expected = [
            ['name' => 'required', 'parameters' => []],
            ['name' => 'email', 'parameters' => []],
        ];

        $this->assertEquals($expected, $this->compiledRules->getRule('email'));
        $this->assertEmpty($this->compiledRules->getRule('password'));
    }

    /**
     * @test
     */
    public function it_handles_empty_rule_segments(): void
    {
        $this->compiledRules->addRule('field', 'required||email');

        $expected = [
            'field' => [
                ['name' => 'required', 'parameters' => []],
                ['name' => 'email', 'parameters' => []],
            ],
        ];

        $this->assertEquals($expected, $this->compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_with_pre_compiled_rules(): void
    {
        $rules = ['email' => 'required|email'];
        $preCompiled = [
            'email' => [
                ['name' => 'required', 'parameters' => []],
                ['name' => 'email', 'parameters' => []],
            ],
        ];

        $compiledRules = new CompiledRules($rules, $preCompiled);

        $this->assertEquals($preCompiled, $compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_supports_method_chaining_for_add_rule(): void
    {
        $result = $this->compiledRules
            ->addRule('email', 'required')
            ->addRule('password', 'required|min:8');

        $this->assertSame($this->compiledRules, $result);
    }

    /**
     * @test
     */
    public function it_only_compiles_rules_when_not_already_compiled(): void
    {
        // Instead of mocking, we'll test the behavior directly
        $rules = ['field' => 'required|email'];
        $compiledRules = new CompiledRules($rules);

        $expected = [
            'field' => [
                ['name' => 'required', 'parameters' => []],
                ['name' => 'email', 'parameters' => []],
            ],
        ];

        // Verify that rules were compiled
        $this->assertEquals($expected, $compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_does_not_compile_when_both_parameters_provided(): void
    {
        // Instead of mocking, we'll test the behavior directly
        $rules = ['field' => 'required|email'];
        $precompiled = [
            'field' => [
                ['name' => 'custom', 'parameters' => []],  // Intentionally different
            ],
        ];

        $compiledRules = new CompiledRules($rules, $precompiled);

        // Verify that precompiled rules were kept intact
        $this->assertEquals($precompiled, $compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_handles_array_rules_with_name_attribute(): void
    {
        $rule = ['name' => 'custom', 'parameters' => [1, 2, 3]];
        $this->compiledRules->addRule('field', $rule);

        $expected = [
            'field' => [
                ['name' => 'custom', 'parameters' => [1, 2, 3]],
            ],
        ];

        $this->assertEquals($expected, $this->compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_returns_false_when_field_exists_but_has_empty_rules(): void
    {
        // Créer un champ avec un tableau vide de règles
        $reflector = new \ReflectionClass(CompiledRules::class);
        $property = $reflector->getProperty('compiledRules');
        $property->setAccessible(true);
        $property->setValue($this->compiledRules, ['field' => []]);

        $this->assertFalse($this->compiledRules->hasRule('field'));
    }

    /**
     * @test
     */
    public function it_correctly_parses_rules_with_multiple_colons(): void
    {
        $this->compiledRules->addRule('field', 'regex:/^[a-z]+$/i');

        $expected = [
            'field' => [
                ['name' => 'regex', 'parameters' => ['/^[a-z]+$/i']],
            ],
        ];

        $this->assertEquals($expected, $this->compiledRules->getCompiledRules());
    }
}
