<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Contracts\RuleInterface;
use ValidatorPro\Core\Validator;
use ValidatorPro\Rules\Common\RequiredRule;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
        $this->validator->addRule('required', new RequiredRule());
    }

    /**
     * @test
     */
    public function it_registers_default_rules_during_construction(): void
    {
        $this->assertTrue($this->validator->hasRule('nullable'));
    }

    /**
     * @test
     */
    public function it_validates_field_with_single_rule(): void
    {
        $rules = ['name' => 'required'];
        $data = ['name' => 'John'];

        $result = $this->validator->validate($data, $rules);

        $this->assertTrue($result->passes());
        $this->assertEquals(['name' => 'John'], $result->getValidData());
    }

    /**
     * @test
     */
    public function it_fails_validation_for_missing_required_field(): void
    {
        $rules = ['name' => 'required'];
        $data = ['email' => 'john@example.com'];

        $result = $this->validator->validate($data, $rules);

        $this->assertTrue($result->fails());
        $this->assertNotEmpty($result->getErrors());
    }

    /**
     * @test
     */
    public function it_allows_null_value_for_nullable_field(): void
    {
        $rules = ['email' => 'nullable|required'];
        $data = ['email' => null];

        $result = $this->validator->validate($data, $rules);

        $this->assertTrue($result->passes());
        $this->assertEmpty($result->getErrors());
        $this->assertEquals(['email' => null], $result->getValidData());
    }

    /**
     * @test
     */
    public function it_compiles_string_rules_correctly(): void
    {
        $rules = 'required|nullable';
        $compiled = $this->validator->compile($rules);

        $compiledRules = $compiled->getCompiledRules();

        // Vérifier qu'il y a un champ '_default'
        $this->assertArrayHasKey('_default', $compiledRules);

        // Vérifier que ce champ contient 2 règles
        $this->assertCount(2, $compiledRules['_default']);

        // Vérifier que les règles attendues sont présentes
        $this->assertArrayHasKey('required', $compiledRules['_default']);
        $this->assertArrayHasKey('nullable', $compiledRules['_default']);
    }

    /**
     * @test
     */
    public function it_adds_callable_rule_correctly(): void
    {
        $callable = function ($value) {
            return $value === 'valid';
        };

        $this->validator->addRule('custom', $callable);

        $rules = ['field' => 'custom'];
        $data = ['field' => 'valid'];

        $result = $this->validator->validate($data, $rules);

        $this->assertTrue($result->passes());
    }

    /**
     * @test
     */
    public function it_replaces_attribute_placeholder_in_error_message(): void
    {
        $rules = ['email' => 'required'];
        $data = [];

        $result = $this->validator->validate($data, $rules);

        $error = $result->getError('email');
        $this->assertStringContainsString('email', $error);
        $this->assertStringNotContainsString(':attribute', $error);
    }

    /**
     * @test
     */
    public function it_uses_custom_error_message_if_provided(): void
    {
        $rules = ['email' => 'required'];
        $messages = ['email.required' => 'Please provide your email address'];
        $data = [];

        $result = $this->validator->validate($data, $rules, $messages);
        $error = $result->getError('email');

        $this->assertEquals('Please provide your email address', $error);
    }

    /**
     * @test
     */
    public function it_stops_checking_rules_after_first_failure(): void
    {
        // Définir un compteur pour suivre le nombre d'appels à validate
        $validateCalls = 0;

        // Créer une règle personnalisée qui échoue toujours et incrémente le compteur
        $failingRule = function ($value) use (&$validateCalls) {
            $validateCalls++;

            return false;
        };

        // Créer une autre règle qui incrémente également le compteur
        $secondRule = function ($value) use (&$validateCalls) {
            $validateCalls++;

            return true;
        };

        // Ajouter les règles
        $this->validator->addRule('always_fails', $failingRule);
        $this->validator->addRule('second_rule', $secondRule);

        // Valider avec les deux règles
        $result = $this->validator->validate(
            ['field' => 'value'],
            ['field' => 'always_fails|second_rule']
        );

        // La validation doit échouer
        $this->assertTrue($result->fails());

        // Le compteur ne doit être incrémenté qu'une fois car la validation
        // devrait s'arrêter après le premier échec
        $this->assertEquals(1, $validateCalls);
    }

    /**
     * @test
     */
    public function it_correctly_processes_nullable_fields(): void
    {
        // Créer une règle qui échoue si elle est appelée
        $shouldNotBeCalledRule = function ($value) {};

        $this->validator->addRule('should_not_be_called', $shouldNotBeCalledRule);

        // Valider avec nullable et la règle qui ne devrait pas être appelée
        $result = $this->validator->validate(
            ['field' => null],
            ['field' => 'nullable|should_not_be_called']
        );

        // La validation doit réussir
        $this->assertTrue($result->passes());

        // Le champ doit être marqué comme validé
        $this->assertContains('field', $result->getValidatedFields());

        // Le champ doit être dans les données valides
        $this->assertArrayHasKey('field', $result->getValidData());
        $this->assertNull($result->getValidData()['field']);
    }

    /**
     * @test
     */
    public function it_prioritizes_field_rule_specific_messages(): void
    {
        $rules = ['email' => 'required'];
        $messages = [
            'email.required' => 'Field.Rule specific',
            'email' => 'Field specific',
            'required' => 'Rule specific',
        ];
        $data = [];

        $result = $this->validator->validate($data, $rules, $messages);
        $error = $result->getError('email');

        $this->assertEquals('Field.Rule specific', $error);
    }

    /**
     * @test
     */
    public function it_uses_field_specific_message_when_field_rule_not_available(): void
    {
        $rules = ['email' => 'required'];
        $messages = [
            'email' => 'Field specific',
            'required' => 'Rule specific',
        ];
        $data = [];

        $result = $this->validator->validate($data, $rules, $messages);
        $error = $result->getError('email');

        $this->assertEquals('Field specific', $error);
    }

    /**
     * @test
     */
    public function it_uses_rule_specific_message_when_others_not_available(): void
    {
        $rules = ['email' => 'required'];
        $messages = [
            'required' => 'Rule specific',
        ];
        $data = [];

        $result = $this->validator->validate($data, $rules, $messages);
        $error = $result->getError('email');

        $this->assertEquals('Rule specific', $error);
    }

    /**
     * @test
     */
    public function it_wraps_callable_in_rule_interface(): void
    {
        $callable = function ($value) {
            return $value === 'valid';
        };

        $this->validator->addRule('callable_rule', $callable);

        // Vérifier que la règle existe
        $this->assertTrue($this->validator->hasRule('callable_rule'));

        // Vérifier que c'est une instance de RuleInterface
        $rule = $this->validator->getRule('callable_rule');
        $this->assertInstanceOf(RuleInterface::class, $rule);

        // Vérifier que la règle fonctionne correctement
        $this->assertTrue($rule->validate('valid'));
        $this->assertFalse($rule->validate('invalid'));
    }

    /**
     * @test
     */
    public function it_does_not_wrap_rule_interface_instances(): void
    {
        $originalRule = new RequiredRule();
        $this->validator->addRule('original_rule', $originalRule);

        // Vérifier qu'on récupère bien l'instance originale
        $retrievedRule = $this->validator->getRule('original_rule');
        $this->assertSame($originalRule, $retrievedRule);
    }

    /**
     * @test
     */
    public function it_parses_rules_with_parameters_correctly(): void
    {
        // Cette méthode est privée, nous devons donc la tester indirectement
        $rules = 'required|min:5|between:10,20|in:a,b,c';
        $compiled = $this->validator->compile($rules);

        $compiledRules = $compiled->getCompiledRules()['_default'];

        // Vérifier les paramètres
        $this->assertEmpty($compiledRules['required']);
        $this->assertEquals(['5'], $compiledRules['min']);
        $this->assertEquals(['10', '20'], $compiledRules['between']);
        $this->assertEquals(['a', 'b', 'c'], $compiledRules['in']);
    }

    /**
     * @test
     */
    public function it_handles_quoted_parameters_in_rules(): void
    {
        $rules = 'regex:"^[a-z]+$"|in:"value,with,commas","another value"';
        $compiled = $this->validator->compile($rules);

        $compiledRules = $compiled->getCompiledRules()['_default'];

        $this->assertEquals(['^[a-z]+$'], $compiledRules['regex']);
        $this->assertEquals(['value,with,commas', 'another value'], $compiledRules['in']);
    }
}
