<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Contracts\CompiledRulesInterface;
use ValidatorPro\Contracts\ContextInterface;
use ValidatorPro\Contracts\RuleInterface;
use ValidatorPro\Contracts\RuleRegistryInterface;
use ValidatorPro\Contracts\ValidationResultInterface;
use ValidatorPro\Core\CompiledRules;
use ValidatorPro\Core\Validator;

class ValidatorTest extends TestCase
{
    /**
     * @var Validator
     */
    private Validator $validator;

    /**
     * @var RuleRegistryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $ruleRegistry;

    protected function setUp(): void
    {
        $this->ruleRegistry = $this->createMock(RuleRegistryInterface::class);
        $this->validator = new Validator($this->ruleRegistry);
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_with_rule_registry(): void
    {
        $this->assertInstanceOf(Validator::class, $this->validator);
    }

    /**
     * @test
     */
    public function validate_returns_validation_result_interface(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(true);
        $mockRule->method('getMessage')->willReturn('Error message');

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        $result = $this->validator->validate(['field' => 'value'], ['field' => 'rule']);

        $this->assertInstanceOf(ValidationResultInterface::class, $result);
    }

    /**
     * @test
     */
    public function validate_with_passing_rules_returns_valid_result(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(true);
        $mockRule->method('getMessage')->willReturn('Error message');
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        $result = $this->validator->validate(['field' => 'value'], ['field' => 'rule']);

        $this->assertTrue($result->passes());
        $this->assertFalse($result->fails());
        $this->assertEmpty($result->getErrors());
    }

    /**
     * @test
     */
    public function validate_with_failing_rules_returns_invalid_result(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(false);
        $mockRule->method('getMessage')->willReturn('Error message');
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        $result = $this->validator->validate(['field' => 'value'], ['field' => 'rule']);

        $this->assertFalse($result->passes());
        $this->assertTrue($result->fails());
        $this->assertNotEmpty($result->getErrors());
        $this->assertArrayHasKey('field', $result->getErrors());
    }

    /**
     * @test
     */
    public function validate_with_string_rules_works_correctly(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(true);
        $mockRule->method('getMessage')->willReturn('Error message');
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        $result = $this->validator->validate(['field' => 'value'], 'rule');

        $this->assertTrue($result->passes());
    }

    /**
     * @test
     */
    public function validate_with_multiple_rules_stops_on_first_failure(): void
    {
        $mockPassingRule = $this->createMock(RuleInterface::class);
        $mockPassingRule->method('validate')->willReturn(true);
        $mockPassingRule->method('setParameters')->willReturnSelf();

        $mockFailingRule = $this->createMock(RuleInterface::class);
        $mockFailingRule->method('validate')->willReturn(false);
        $mockFailingRule->method('getMessage')->willReturn('Error message');
        $mockFailingRule->method('setParameters')->willReturnSelf();

        // Configure the rule registry to return different rules based on name
        $this->ruleRegistry->method('get')
            ->willReturnCallback(function ($name) use ($mockPassingRule, $mockFailingRule) {
                if ($name === 'passing_rule') {
                    return $mockPassingRule;
                } elseif ($name === 'failing_rule') {
                    return $mockFailingRule;
                }

                return null;
            });

        $rules = [
            'field' => 'passing_rule|failing_rule|another_rule',
        ];

        $result = $this->validator->validate(['field' => 'value'], $rules);

        $this->assertTrue($result->fails());
        $this->assertCount(1, $result->getFieldErrors('field')); // Only one error, stopped after first failure
    }

    /**
     * @test
     */
    public function validate_with_custom_messages_uses_custom_messages(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(false);
        $mockRule->method('getMessage')->willReturn('Default error message');
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        $customMessage = 'Custom error message';
        $result = $this->validator->validate(
            ['field' => 'value'],
            ['field' => 'rule'],
            ['rule' => $customMessage]
        );

        $this->assertTrue($result->fails());
        $this->assertEquals($customMessage, $result->getError('field'));
    }

    /**
     * @test
     */
    public function validate_with_field_specific_custom_messages_uses_correct_message(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(false);
        $mockRule->method('getMessage')->willReturn('Default error message');
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        $fieldSpecificMessage = 'Field specific message';
        $result = $this->validator->validate(
            ['field' => 'value'],
            ['field' => 'rule'],
            ['field.rule' => $fieldSpecificMessage]
        );

        $this->assertTrue($result->fails());
        $this->assertEquals($fieldSpecificMessage, $result->getError('field'));
    }

    /**
     * @test
     */
    public function validate_with_context_merges_context_rules(): void
    {
        $mockContext = $this->createMock(ContextInterface::class);
        $mockContext->method('getRules')->willReturn(['contextField' => 'rule']);
        $mockContext->method('getMessages')->willReturn([]);

        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(true);
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        $this->validator->setContext($mockContext);

        $result = $this->validator->validate(
            ['field' => 'value', 'contextField' => 'value'],
            ['field' => 'rule']
        );

        // Both field and contextField should pass validation
        $this->assertTrue($result->passes());
    }

    /**
     * @test
     */
    public function validate_with_context_merges_context_messages(): void
    {
        $contextMessage = 'Context error message';

        $mockContext = $this->createMock(ContextInterface::class);
        $mockContext->method('getRules')->willReturn([]);
        $mockContext->method('getMessages')->willReturn(['rule' => $contextMessage]);

        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(false);
        $mockRule->method('getMessage')->willReturn('Default error message');
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        $this->validator->setContext($mockContext);

        $result = $this->validator->validate(['field' => 'value'], ['field' => 'rule']);

        $this->assertTrue($result->fails());
        $this->assertEquals($contextMessage, $result->getError('field'));
    }

    /**
     * @test
     */
    public function validate_with_custom_extensions_works_correctly(): void
    {
        $extensionName = 'custom_extension';
        $extensionCalled = false;

        $extension = function ($value, $parameters, $data) use (&$extensionCalled) {
            $extensionCalled = true;

            return $value === 'valid';
        };

        $this->validator->extend($extensionName, $extension);

        // Test with passing validation
        $result = $this->validator->validate(['field' => 'valid'], ['field' => $extensionName]);
        $this->assertTrue($result->passes());
        $this->assertTrue($extensionCalled);

        // Reset and test with failing validation
        $extensionCalled = false;
        $result = $this->validator->validate(['field' => 'invalid'], ['field' => $extensionName]);
        $this->assertTrue($result->fails());
        $this->assertTrue($extensionCalled);
    }

    /**
     * @test
     */
    public function add_rule_adds_rule_to_registry(): void
    {
        $ruleName = 'test_rule';
        $mockRule = $this->createMock(RuleInterface::class);

        $this->ruleRegistry->expects($this->once())
            ->method('register')
            ->with($ruleName, $mockRule);

        $result = $this->validator->addRule($ruleName, $mockRule);

        $this->assertSame($this->validator, $result);
    }

    /**
     * @test
     */
    public function add_rule_throws_exception_for_callable(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->validator->addRule('test_rule', function () {
            return true;
        });
    }

    /**
     * @test
     */
    public function set_context_sets_context_and_returns_self(): void
    {
        $mockContext = $this->createMock(ContextInterface::class);

        $result = $this->validator->setContext($mockContext);

        $this->assertSame($this->validator, $result);
    }

    /**
     * @test
     */
    public function compile_with_string_creates_compiled_rules_with_default_field(): void
    {
        $rules = 'required|email';

        $compiledRules = $this->validator->compile($rules);

        $this->assertInstanceOf(CompiledRulesInterface::class, $compiledRules);
        $this->assertTrue($compiledRules->hasRule('_default'));
    }

    /**
     * @test
     */
    public function compile_with_array_creates_compiled_rules(): void
    {
        $rules = ['field' => 'required|email'];

        $compiledRules = $this->validator->compile($rules);

        $this->assertInstanceOf(CompiledRulesInterface::class, $compiledRules);
        $this->assertTrue($compiledRules->hasRule('field'));
    }

    /**
     * @test
     */
    public function compile_with_context_merges_context_rules(): void
    {
        $contextRules = ['contextField' => 'required'];
        $rules = ['field' => 'required|email'];

        $mockContext = $this->createMock(ContextInterface::class);
        $mockContext->method('getRules')->willReturn($contextRules);

        $this->validator->setContext($mockContext);

        $compiledRules = $this->validator->compile($rules);

        $this->assertInstanceOf(CompiledRulesInterface::class, $compiledRules);
        $this->assertTrue($compiledRules->hasRule('field'));
        $this->assertTrue($compiledRules->hasRule('contextField'));
    }

    /**
     * @test
     */
    public function extend_adds_extension_and_returns_self(): void
    {
        $extensionName = 'custom_extension';
        $extension = function () {
            return true;
        };

        $result = $this->validator->extend($extensionName, $extension);

        $this->assertSame($this->validator, $result);
    }

    /**
     * @test
     */
    public function get_rule_returns_rule_from_registry(): void
    {
        $ruleName = 'test_rule';
        $mockRule = $this->createMock(RuleInterface::class);

        $this->ruleRegistry->method('get')
            ->with($ruleName)
            ->willReturn($mockRule);

        $result = $this->validator->getRule($ruleName);

        $this->assertSame($mockRule, $result);
    }

    /**
     * @test
     */
    public function has_rule_returns_result_from_registry(): void
    {
        $ruleName = 'test_rule';

        $this->ruleRegistry->method('has')
            ->with($ruleName)
            ->willReturn(true);

        $result = $this->validator->hasRule($ruleName);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function get_available_rules_returns_all_rules_from_registry(): void
    {
        $mockRules = ['rule1' => $this->createMock(RuleInterface::class)];

        $this->ruleRegistry->method('all')
            ->willReturn($mockRules);

        $result = $this->validator->getAvailableRules();

        $this->assertSame($mockRules, $result);
    }

    /**
     * @test
     */
    public function validate_rule_throws_exception_for_unknown_rule(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->ruleRegistry->method('get')->willReturn(null);

        $this->validator->validate(['field' => 'value'], ['field' => 'unknown_rule']);
    }

    /**
     * @test
     */
    public function validate_with_dot_notation_field_accesses_nested_data(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')
            ->willReturnCallback(function ($value) {
                return $value === 'nested_value';
            });
        $mockRule->method('getMessage')->willReturn('Error message');
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        $data = [
            'parent' => [
                'child' => 'nested_value',
            ],
        ];

        $rules = [
            'parent.child' => 'rule',
        ];

        $result = $this->validator->validate($data, $rules);

        $this->assertTrue($result->passes());
    }

    /**
     * @test
     */
    public function validate_with_parameters_passes_parameters_to_rule(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->expects($this->once())
            ->method('setParameters')
            ->with($this->equalTo(['5']));
        $mockRule->method('validate')->willReturn(true);

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        $this->validator->validate(['field' => 'value'], ['field' => 'rule:5']);
    }

    /**
     * @test
     */
    public function format_message_replaces_attribute_placeholder(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(false);
        $mockRule->method('getMessage')->willReturn('The :attribute field is required.');
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        $result = $this->validator->validate(['field' => ''], ['field' => 'rule']);

        $this->assertTrue($result->fails());
        $this->assertStringContainsString('field', $result->getError('field'));
        $this->assertStringNotContainsString(':attribute', $result->getError('field'));
    }

    /**
     * @test
     */
    public function format_message_replaces_parameter_placeholders(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(false);
        $mockRule->method('getMessage')->willReturn('The :attribute must be at least :param0 characters.');
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        $result = $this->validator->validate(['field' => 'abc'], ['field' => 'rule:5']);

        $this->assertTrue($result->fails());
        $error = $result->getError('field');
        $this->assertStringContainsString('5', $error);
        $this->assertStringNotContainsString(':param0', $error);
    }

    /**
     * @test
     */
    public function format_message_replaces_named_parameter_placeholders(): void
    {
        $ruleWithCallback = $this->getMockBuilder(RuleInterface::class)
            ->getMock();
        $ruleWithCallback->method('validate')->willReturn(false);
        $ruleWithCallback->method('getMessage')->willReturn('The :attribute must be a valid :type.');
        $ruleWithCallback->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($ruleWithCallback);

        // Create a compiled rules object with a named parameter
        $compiledRules = new CompiledRules([
            'field' => [
                [
                    'name' => 'rule',
                    'parameters' => ['type' => 'email'],
                ],
            ],
        ]);

        // Use reflection to call validateRule directly with the named parameter
        $reflectionMethod = new \ReflectionMethod(Validator::class, 'validateRule');
        $reflectionMethod->setAccessible(true);

        $errorMessage = $reflectionMethod->invoke(
            $this->validator,
            'field',
            'value',
            ['name' => 'rule', 'parameters' => ['type' => 'email']],
            [],
            []
        );

        $this->assertStringContainsString('email', $errorMessage);
        $this->assertStringNotContainsString(':type', $errorMessage);
    }

    /**
     * @test
     */
    public function get_value_for_field_returns_null_for_missing_nested_field(): void
    {
        $data = [
            'parent' => [
                'child' => 'value',
            ],
        ];

        // Use reflection to call getValueForField directly
        $reflectionMethod = new \ReflectionMethod(Validator::class, 'getValueForField');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invoke($this->validator, 'parent.missing', $data);

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function validate_with_mixed_rules_formats_works_correctly(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(true);
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        // Test with different rule formats
        $rules = [
            'field1' => 'rule1|rule2',             // String with pipe
            'field2' => ['rule1', 'rule2'],        // Array of strings
            'field3' => [                          // Array of arrays
                ['name' => 'rule1', 'parameters' => ['param1']],
                ['name' => 'rule2', 'parameters' => ['param2']],
            ],
        ];

        $result = $this->validator->validate(
            ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'],
            $rules
        );

        $this->assertTrue($result->passes());
    }

    /**
     * @test
     */
    public function validate_skips_only_default_field_when_multiple_fields_exist(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(true);
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        // Créer un compiledRules simulé pour tester le comportement exact
        $compiledRulesData = [
            '_default' => [['name' => 'rule', 'parameters' => []]],
            'field1' => [['name' => 'rule', 'parameters' => []]],
        ];

        // Créer un mock pour CompiledRules pour vérifier que _default est ignoré
        $mockCompiledRules = $this->createMock(CompiledRulesInterface::class);
        $mockCompiledRules->method('getCompiledRules')->willReturn($compiledRulesData);

        // Utiliser Reflection pour accéder aux méthodes privées
        $reflectionMethod = new \ReflectionMethod(Validator::class, 'compile');
        $reflectionMethod->setAccessible(true);

        // Remplacer notre méthode compile pour retourner notre mock contrôlé
        $validatorMock = $this->getMockBuilder(Validator::class)
            ->setConstructorArgs([$this->ruleRegistry])
            ->onlyMethods(['compile'])
            ->getMock();
        $validatorMock->method('compile')->willReturn($mockCompiledRules);

        // Exécuter la validation qui devrait ignorer _default
        $result = $validatorMock->validate(
            ['field1' => 'value1'],
            ['field1' => 'rule']
        );

        // Vérifier que seul field1 a été validé et pas _default
        $this->assertTrue($result->passes());
        $this->assertCount(0, $result->getErrors());
    }

    /**
     * @test
     */
    public function validate_adds_field_to_valid_data_if_exists_in_original_data(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(true);
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        // Tester les deux conditions séparément
        // 1. Le champ existe directement dans les données
        $result1 = $this->validator->validate(
            ['field1' => 'value1'],
            ['field1' => 'rule']
        );
        $this->assertTrue($result1->passes());
        $this->assertEquals(['field1' => 'value1'], $result1->getValidData());

        // 2. Le champ n'existe pas directement mais peut être récupéré par getValueForField
        $nestedData = ['parent' => ['child' => 'nested_value']];
        $result2 = $this->validator->validate(
            $nestedData,
            ['parent.child' => 'rule']
        );
        $this->assertTrue($result2->passes());

        // 3. Le champ n'existe ni directement ni via getValueForField
        $result3 = $this->validator->validate(
            ['field1' => 'value1'],
            ['nonexistent' => 'rule']
        );
        $this->assertTrue($result3->passes());
        $this->assertArrayNotHasKey('nonexistent', $result3->getValidData());
    }

    /**
     * @test
     */
    public function validate_keeps_all_valid_data_that_passes_validation(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(true);
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        $data = ['field1' => 'value1', 'field2' => 'value2'];
        $result = $this->validator->validate($data, ['field1' => 'rule', 'field2' => 'rule']);

        $this->assertTrue($result->passes());
        $this->assertEquals($data, $result->getValidData());

        // Vérifier explicitement que la boucle finale fonctionne
        // en ajoutant un champ avec erreur
        $mockFailingRule = $this->createMock(RuleInterface::class);
        $mockFailingRule->method('validate')->willReturn(false);
        $mockFailingRule->method('getMessage')->willReturn('Error');
        $mockFailingRule->method('setParameters')->willReturnSelf();

        // Modifier le comportement du registry pour retourner différentes règles
        $this->ruleRegistry = $this->createMock(RuleRegistryInterface::class);
        $this->ruleRegistry->method('get')
            ->willReturnCallback(function ($name) use ($mockRule, $mockFailingRule) {
                return $name === 'fail' ? $mockFailingRule : $mockRule;
            });

        $validator = new Validator($this->ruleRegistry);
        $data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $rules = ['field1' => 'rule', 'field2' => 'fail', 'field3' => 'rule'];

        $result = $validator->validate($data, $rules);

        $this->assertTrue($result->fails());
        $expectedValidData = ['field1' => 'value1', 'field3' => 'value3'];
        $this->assertEquals($expectedValidData, $result->getValidData());
    }

    /**
     * @test
     */
    public function add_rule_handles_different_input_types_correctly(): void
    {
        // 1. Test avec une RuleInterface valide
        $ruleName = 'test_rule';
        $mockRule = $this->createMock(RuleInterface::class);

        $this->ruleRegistry->expects($this->once())
            ->method('register')
            ->with($ruleName, $mockRule);

        $result = $this->validator->addRule($ruleName, $mockRule);
        $this->assertSame($this->validator, $result);

        // 2. Test avec un callable qui n'est pas une RuleInterface
        $callable = function () { return true; };

        try {
            $this->validator->addRule('callable_rule', $callable);
            $this->fail('Exception should have been thrown');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('Callable rules are not supported', $e->getMessage());
        }

        // 3. Test avec un objet qui n'est ni callable ni RuleInterface
        $object = new \stdClass();

        // Cela ne devrait pas enregistrer l'objet ni lancer d'exception
        $this->ruleRegistry->expects($this->never())
            ->method('register')
            ->with('object_rule', $object);

        $result = $this->validator->addRule('object_rule', $object);
        $this->assertSame($this->validator, $result);
    }

    /**
     * @test
     */
    public function validate_field_returns_all_errors(): void
    {
        // Créer deux règles qui échouent
        $mockRule1 = $this->createMock(RuleInterface::class);
        $mockRule1->method('validate')->willReturn(false);
        $mockRule1->method('getMessage')->willReturn('Error 1');
        $mockRule1->method('setParameters')->willReturnSelf();

        $mockRule2 = $this->createMock(RuleInterface::class);
        $mockRule2->method('validate')->willReturn(false);
        $mockRule2->method('getMessage')->willReturn('Error 2');
        $mockRule2->method('setParameters')->willReturnSelf();

        // Modifier pour retourner différentes règles
        $this->ruleRegistry = $this->createMock(RuleRegistryInterface::class);
        $this->ruleRegistry->method('get')
            ->willReturnCallback(function ($name) use ($mockRule1, $mockRule2) {
                if ($name === 'rule1') {
                    return $mockRule1;
                }
                if ($name === 'rule2') {
                    return $mockRule2;
                }

                return null;
            });

        $validator = new Validator($this->ruleRegistry);

        // Utiliser la réflexion pour accéder à validateField
        $reflectionMethod = new \ReflectionMethod(Validator::class, 'validateField');
        $reflectionMethod->setAccessible(true);

        // Appeler validateField avec les deux règles
        $errors = $reflectionMethod->invoke(
            $validator,
            'field',
            'value',
            [
                ['name' => 'rule1', 'parameters' => []],
                ['name' => 'rule2', 'parameters' => []],
            ],
            [],
            []
        );

        // Vérifier que nous obtenons les deux erreurs
        // Ou si la validation s'arrête au premier échec, qu'il y a une erreur
        $this->assertNotEmpty($errors);
        // Selon votre implémentation, cela pourrait être 1 ou 2
        // Si votre code s'arrête à la première erreur :
        $this->assertCount(1, $errors);
        $this->assertEquals('Error 1', $errors[0]);
    }

    /**
     * @test
     */
    public function get_error_message_respects_message_priority(): void
    {
        // Créer des messages à différents niveaux de priorité
        $fieldSpecificMessage = 'Field specific message';
        $ruleSpecificMessage = 'Rule specific message';
        $defaultMessage = 'Default message';
        $fallbackMessage = 'The field validation failed.';

        $customMessages = [
            'field.rule' => $fieldSpecificMessage,
            'rule' => $ruleSpecificMessage,
        ];

        // Utiliser la réflexion pour accéder à getErrorMessage
        $reflectionMethod = new \ReflectionMethod(Validator::class, 'getErrorMessage');
        $reflectionMethod->setAccessible(true);

        // 1. Test avec tous les messages disponibles (devrait utiliser field.rule)
        $message1 = $reflectionMethod->invoke(
            $this->validator,
            'field',
            'rule',
            [],
            $customMessages,
            $defaultMessage
        );
        $this->assertEquals($fieldSpecificMessage, $message1);

        // 2. Test sans message spécifique au champ (devrait utiliser rule)
        $customMessages2 = ['rule' => $ruleSpecificMessage];
        $message2 = $reflectionMethod->invoke(
            $this->validator,
            'field',
            'rule',
            [],
            $customMessages2,
            $defaultMessage
        );
        $this->assertEquals($ruleSpecificMessage, $message2);

        // 3. Test sans aucun message personnalisé (devrait utiliser default)
        $message3 = $reflectionMethod->invoke(
            $this->validator,
            'field',
            'rule',
            [],
            [],
            $defaultMessage
        );
        $this->assertEquals($defaultMessage, $message3);

        // 4. Test sans aucun message (devrait utiliser fallback)
        $message4 = $reflectionMethod->invoke(
            $this->validator,
            'field',
            'rule',
            [],
            [],
            null
        );
        $this->assertStringContainsString('field', $message4);
        $this->assertStringContainsString('validation failed', $message4);
    }

    /**
     * @test
     */
    public function format_message_converts_non_string_parameters_to_strings(): void
    {
        // Test avec un paramètre qui n'est pas une chaîne
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(false);
        $mockRule->method('getMessage')->willReturn('The :attribute must be at least :param0.');
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        // Utiliser un paramètre qui n'est pas une chaîne (int, float, bool, etc.)
        $reflectionMethod = new \ReflectionMethod(Validator::class, 'formatMessage');
        $reflectionMethod->setAccessible(true);

        // Test avec un entier
        $message1 = $reflectionMethod->invoke(
            $this->validator,
            'The :attribute must be at least :param0.',
            'field',
            [123]
        );
        $this->assertEquals('The field must be at least 123.', $message1);

        // Test avec un booléen
        $message2 = $reflectionMethod->invoke(
            $this->validator,
            'The :attribute must be :param0.',
            'field',
            [true]
        );
        $this->assertEquals('The field must be 1.', $message2);

        // Test avec paramètre nommé qui n'est pas une chaîne
        $message3 = $reflectionMethod->invoke(
            $this->validator,
            'The :attribute must be :type.',
            'field',
            ['type' => 123]
        );
        $this->assertEquals('The field must be 123.', $message3);
    }

    /**
     * @test
     */
    public function validate_correctly_handles_default_field_with_single_rules(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(true);
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        // Cas avec exactement 1 règle (count($compiledRules->getCompiledRules()) == 1)
        $data = ['field' => 'value'];

        // Utiliser la réflexion pour tester spécifiquement le comportement
        $reflectionMethod = new \ReflectionMethod(Validator::class, 'compile');
        $reflectionMethod->setAccessible(true);

        $compiledRules = $reflectionMethod->invoke($this->validator, 'rule');

        // Vérifier qu'il y a exactement une règle et qu'elle est pour _default
        $this->assertCount(1, $compiledRules->getCompiledRules());
        $this->assertTrue($compiledRules->hasRule('_default'));

        // Maintenant testons la validation
        $result = $this->validator->validate($data, 'rule');

        // S'assurer que la validation passe
        $this->assertTrue($result->passes());
        $this->assertNotEmpty($result->getValidData());
    }

    /**
     * @test
     */
    public function validate_breaks_loop_when_field_is_default_with_multiple_fields(): void
    {
        // Créer un mock de règle qui réussit toujours
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(true);
        $mockRule->method('setParameters')->willReturnSelf();

        // Créer un registre qui retourne toujours notre règle
        $ruleRegistry = $this->createMock(RuleRegistryInterface::class);
        $ruleRegistry->method('get')->willReturn($mockRule);

        // Créer un Validator avec notre registre
        $validator = new Validator($ruleRegistry);

        // Préparer des données avec _default et un autre champ
        $data = ['field1' => 'value1'];

        // Créer manuellement un CompiledRules avec _default et field1
        $compiledRules = new CompiledRules([
            '_default' => 'rule',
            'field1' => 'rule',
        ]);

        // Utiliser la réflexion pour remplacer notre méthode compile
        $reflectionClass = new \ReflectionClass($validator);
        $reflectionMethod = $reflectionClass->getMethod('compile');
        $reflectionMethod->setAccessible(true);

        // Utiliser un mock partiel pour simuler compile
        $validatorMock = $this->getMockBuilder(Validator::class)
            ->setConstructorArgs([$ruleRegistry])
            ->onlyMethods(['compile'])
            ->getMock();

        $validatorMock->method('compile')->willReturn($compiledRules);

        // Exécuter la validation
        $result = $validatorMock->validate($data, ['field1' => 'rule']);

        // Vérifier que le résultat est valide
        $this->assertTrue($result->passes());

        // Si _default est ignoré correctement, seul field1 devrait être dans le résultat
        $this->assertArrayHasKey('field1', $result->getValidData());
        $this->assertCount(1, $result->getValidData());
    }

    /**
     * @test
     */
    public function validate_adds_field_to_valid_data_if_exists_in_original_data_or_nested(): void
    {
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(true);
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        // Test avec un champ qui existe directement
        $data1 = ['field1' => 'value1'];
        $result1 = $this->validator->validate($data1, ['field1' => 'rule']);
        $this->assertTrue($result1->passes());
        $this->assertEquals($data1, $result1->getValidData());

        // Test avec un champ qui n'existe pas directement mais via données imbriquées
        $data2 = ['parent' => ['child' => 'value2']];
        $result2 = $this->validator->validate($data2, ['parent.child' => 'rule']);
        $this->assertTrue($result2->passes());

        // Vérifier que parent.child est dans les données valides
        // Remarque: selon votre implémentation, ce pourrait être 'parent.child' ou une structure imbriquée
        $validData2 = $result2->getValidData();
        $this->assertNotEmpty($validData2);

        // Test avec un champ qui n'existe pas du tout
        $data3 = ['field1' => 'value1'];
        $result3 = $this->validator->validate($data3, ['nonexistent' => 'rule']);
        $this->assertTrue($result3->passes());

        // Le champ nonexistent ne devrait pas être dans les données valides
        $this->assertArrayNotHasKey('nonexistent', $result3->getValidData());
    }

    /**
     * @test
     */
    public function validate_field_returns_all_errors_correctly(): void
    {
        // Créer deux règles dont une échoue
        $mockPassingRule = $this->createMock(RuleInterface::class);
        $mockPassingRule->method('validate')->willReturn(true);
        $mockPassingRule->method('setParameters')->willReturnSelf();

        $mockFailingRule = $this->createMock(RuleInterface::class);
        $mockFailingRule->method('validate')->willReturn(false);
        $mockFailingRule->method('getMessage')->willReturn('Error message');
        $mockFailingRule->method('setParameters')->willReturnSelf();

        // Configurer le registre pour retourner la règle appropriée selon le nom
        $ruleRegistry = $this->createMock(RuleRegistryInterface::class);
        $ruleRegistry->method('get')
            ->willReturnCallback(function ($ruleName) use ($mockPassingRule, $mockFailingRule) {
                if ($ruleName === 'passing_rule') {
                    return $mockPassingRule;
                } elseif ($ruleName === 'failing_rule') {
                    return $mockFailingRule;
                }

                return null;
            });

        // Créer le validateur avec notre registre
        $validator = new Validator($ruleRegistry);

        // Tester avec une règle qui échoue
        $result = $validator->validate(['field' => 'value'], ['field' => 'failing_rule']);

        // Vérifier que nous avons une erreur
        $this->assertTrue($result->fails());
        $this->assertCount(1, $result->getFieldErrors('field'));
        $this->assertNotEmpty($result->getError('field'));

        // Tester avec plusieurs règles dont la première échoue (la validation devrait s'arrêter)
        $result2 = $validator->validate(['field' => 'value'], ['field' => 'failing_rule|passing_rule']);

        // On devrait avoir une seule erreur
        $this->assertTrue($result2->fails());
        $this->assertCount(1, $result2->getFieldErrors('field'));
    }

    /**
     * @test
     */
    public function add_rule_handles_callable_rule_correctly(): void
    {
        $callableRule = function () { return true; };

        try {
            $this->validator->addRule('callable_rule', $callableRule);
            $this->fail('Une exception aurait dû être levée');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('Callable rules are not supported', $e->getMessage());
        }

        // Test avec un callable qui est aussi une RuleInterface
        $mockRule = $this->createMock(RuleInterface::class);

        // Rendre l'objet callable
        $callableRuleInterface = new class ($mockRule) implements RuleInterface {
            /**
             * @var RuleInterface
             */
            private RuleInterface $rule;

            /**
             * @param RuleInterface $rule
             */
            public function __construct(RuleInterface $rule)
            {
                $this->rule = $rule;
            }

            /**
             * @return bool
             */
            public function __invoke(): bool
            {
                return true;
            }

            public function validate(mixed $value, array $parameters = [], array $data = []): bool
            {
                return $this->rule->validate($value, $parameters, $data);
            }

            public function getMessage(): string
            {
                return $this->rule->getMessage();
            }

            public function getName(): string
            {
                return $this->rule->getName();
            }

            public function setParameters(array $parameters): RuleInterface
            {
                return $this->rule->setParameters($parameters);
            }

            public function getParameters(): array
            {
                return $this->rule->getParameters();
            }

            public function setErrorMessage(?string $message): RuleInterface
            {
                return $this->rule->setErrorMessage($message);
            }

            public function supportsType(string $type): bool
            {
                return $this->rule->supportsType($type);
            }

            public function getDocumentation(): array
            {
                return $this->rule->getDocumentation();
            }
        };

        // Vérifier que le registre de règles est appelé avec le bon nom et la bonne règle
        $this->ruleRegistry->expects($this->once())
            ->method('register')
            ->with('callable_rule_interface', $callableRuleInterface);

        $this->validator->addRule('callable_rule_interface', $callableRuleInterface);
    }

    /**
     * @test
     */
    public function format_message_handles_non_string_parameters(): void
    {
        // Créer une règle qui échoue et utilise un message avec des placeholders
        $mockRule = $this->createMock(RuleInterface::class);
        $mockRule->method('validate')->willReturn(false);
        $mockRule->method('getMessage')->willReturn('The :attribute must be of type :param0.');
        $mockRule->method('setParameters')->willReturnSelf();

        $this->ruleRegistry->method('get')->willReturn($mockRule);

        // Tester avec un paramètre entier
        $result = $this->validator->validate(['field' => 'value'], ['field' => 'rule:123']);

        $this->assertTrue($result->fails());
        $error = $result->getError('field');
        $this->assertStringContainsString('123', $error);
        $this->assertStringNotContainsString(':param0', $error);

        // Tester avec un paramètre booléen
        $mockRule2 = $this->createMock(RuleInterface::class);
        $mockRule2->method('validate')->willReturn(false);
        $mockRule2->method('getMessage')->willReturn('The :attribute must be :param0.');
        $mockRule2->method('setParameters')->willReturnSelf();

        $this->ruleRegistry = $this->createMock(RuleRegistryInterface::class);
        $this->ruleRegistry->method('get')->willReturn($mockRule2);

        $validator = new Validator($this->ruleRegistry);
        $result2 = $validator->validate(['field' => 'value'], ['field' => 'rule:true']);

        $this->assertTrue($result2->fails());
        $error2 = $result2->getError('field');
        // Selon l'implémentation, 'true' pourrait être converti en '1' ou rester 'true'
        $this->assertTrue(
            strpos($error2, 'true') !== false || strpos($error2, '1') !== false
        );
        $this->assertStringNotContainsString(':param0', $error2);
    }
}
