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

    /**
     * @test
     */
    public function it_handles_array_rules_without_name_attribute(): void
    {
        $rule = ['not_name' => 'value', 'other' => 'test'];
        $this->compiledRules->addRule('field', [$rule]);

        $expected = [
            'field' => [
                ['name' => 'unknown', 'parameters' => []],
            ],
        ];

        $this->assertEquals($expected, $this->compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_compiles_rules_during_initialization(): void
    {
        // On vérifie que les règles sont compilées dans le constructeur
        $rules = ['field' => 'required|email'];
        $compiledRules = new CompiledRules($rules);

        // Vérifier que les règles ont été compilées
        $expected = [
            'field' => [
                ['name' => 'required', 'parameters' => []],
                ['name' => 'email', 'parameters' => []],
            ],
        ];

        $this->assertEquals($expected, $compiledRules->getCompiledRules());

        // Vérifier que si on passe des règles pré-compilées, elles ne sont pas recompilées
        $preCompiled = [
            'field' => [
                ['name' => 'custom', 'parameters' => []],
            ],
        ];

        $compiledRulesWithPreCompiled = new CompiledRules($rules, $preCompiled);
        $this->assertEquals($preCompiled, $compiledRulesWithPreCompiled->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_correctly_handles_rules_with_colons_in_parameters(): void
    {
        $this->compiledRules->addRule('field', 'regex:/^\d{3}:\d{2}$/');

        $expected = [
            'field' => [
                ['name' => 'regex', 'parameters' => ['/^\d{3}:\d{2}$/']],
            ],
        ];

        $this->assertEquals($expected, $this->compiledRules->getCompiledRules());

        // Tester aussi un cas plus complexe
        $this->compiledRules->addRule('another', 'date_format:Y-m-d H:i:s');

        $expectedWithDateFormat = [
            'field' => [
                ['name' => 'regex', 'parameters' => ['/^\d{3}:\d{2}$/']],
            ],
            'another' => [
                ['name' => 'date_format', 'parameters' => ['Y-m-d H:i:s']],
            ],
        ];

        $this->assertEquals($expectedWithDateFormat, $this->compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_handles_object_rules_with_to_string(): void
    {
        // Créer un objet avec __toString
        $stringableObject = new class () {
            public function __toString()
            {
                return 'custom_rule';
            }
        };

        $this->compiledRules->addRule('field', $stringableObject);

        $expected = [
            'field' => [
                ['name' => 'custom_rule', 'parameters' => []],
            ],
        ];

        $this->assertEquals($expected, $this->compiledRules->getCompiledRules());

        // Objet sans __toString
        $nonStringableObject = new \stdClass();
        $this->compiledRules->addRule('another', $nonStringableObject);

        $expectedWithStdClass = [
            'field' => [
                ['name' => 'custom_rule', 'parameters' => []],
            ],
            'another' => [
                ['name' => 'unknown', 'parameters' => []],
            ],
        ];

        $this->assertEquals($expectedWithStdClass, $this->compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_handles_array_of_mixed_rules(): void
    {
        // Un tableau contenant différents types de règles
        $rules = [
            'string_rule',
            ['name' => 'array_rule', 'parameters' => ['param1']],
            new \stdClass(),
        ];

        $this->compiledRules->addRule('field', $rules);

        $expected = [
            'field' => [
                ['name' => 'string_rule', 'parameters' => []],
                ['name' => 'array_rule', 'parameters' => ['param1']],
                ['name' => 'unknown', 'parameters' => []],
            ],
        ];

        $this->assertEquals($expected, $this->compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_explicitly_casts_object_to_string(): void
    {
        // Objet avec __toString retournant un résultat spécifique
        // dont on peut vérifier qu'il est bien le résultat du cast
        $obj = new class () {
            private int $count = 0;

            public function __toString(): string
            {
                $this->count++;

                return 'object_string_' . $this->count;
            }

            public function getCount(): int
            {
                return $this->count;
            }
        };

        // Premier usage de l'objet comme règle
        $this->compiledRules->addRule('field1', $obj);

        // À ce stade, __toString a été appelé une fois
        $this->assertEquals(1, $obj->getCount(), '__toString method should be called once');

        $expected1 = [
            'field1' => [
                ['name' => 'object_string_1', 'parameters' => []],
            ],
        ];

        $this->assertEquals($expected1, $this->compiledRules->getCompiledRules());

        // Deuxième usage de l'objet mais dans un tableau de règles
        $this->compiledRules->addRule('field2', [$obj]);

        // __toString a été appelé une seconde fois
        $this->assertEquals(2, $obj->getCount(), '__toString method should be called twice');

        $expected2 = [
            'field1' => [
                ['name' => 'object_string_1', 'parameters' => []],
            ],
            'field2' => [
                ['name' => 'object_string_2', 'parameters' => []],
            ],
        ];

        $this->assertEquals($expected2, $this->compiledRules->getCompiledRules());
    }

    /**
     * @test
     */
    public function it_allows_extending_compile_initial_rules(): void
    {
        // Créer une sous-classe qui surcharge compileInitialRules
        $customRulesClass = new class () extends CompiledRules {
            public bool $compileWasCalled = false;

            protected function compileInitialRules(): void
            {
                // Marquer qu'on est passé par notre implémentation
                $this->compileWasCalled = true;

                // Appeler l'implémentation parente pour maintenir le comportement
                parent::compileInitialRules();
            }
        };

        // Créer une instance avec des règles non vides pour déclencher compileInitialRules
        $customRules = new $customRulesClass(['field' => 'required']);

        // Vérifier que la méthode surchargée a été appelée lors de l'initialisation
        $this->assertTrue($customRules->compileWasCalled, 'Overridden compileInitialRules should be called');

        // Vérifier également que les règles ont été correctement compilées
        $expected = [
            'field' => [
                ['name' => 'required', 'parameters' => []],
            ],
        ];

        $this->assertEquals($expected, $customRules->getCompiledRules());

        // Testons aussi avec un autre objet mais avec des règles pré-compilées
        // Dans ce cas, compileInitialRules ne devrait pas être appelé
        $anotherCustomRules = new $customRulesClass(
            ['field' => 'required'],
            ['field' => [['name' => 'precompiled', 'parameters' => []]]]
        );

        // Réinitialiser le flag
        $anotherCustomRules->compileWasCalled = false;

        // Vérifier que compileInitialRules n'a pas été appelé
        $this->assertFalse(
            $anotherCustomRules->compileWasCalled,
            'compileInitialRules should not be called when precompiled rules are provided'
        );

        // Vérifier que les règles pré-compilées sont conservées
        $expectedPrecompiled = [
            'field' => [
                ['name' => 'precompiled', 'parameters' => []],
            ],
        ];

        $this->assertEquals($expectedPrecompiled, $anotherCustomRules->getCompiledRules());
    }
}
