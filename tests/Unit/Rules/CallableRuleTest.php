<?php

namespace Tests\Unit\Rules;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Rules\CallableRule;

class CallableRuleTest extends TestCase
{
    /**
     * @test
     */
    public function it_initializes_with_parent_constructor(): void
    {
        $callable = function ($value) { return true; };
        $rule = new CallableRule($callable, 'test_rule');

        // Vérifier que le nom est correctement initialisé
        $this->assertEquals('test_rule', $rule->getName());

        // Vérifier que le message est défini
        $this->assertNotEmpty($rule->getMessage());
    }

    /**
     * @test
     */
    public function it_executes_callable_on_validate(): void
    {
        $called = false;
        $callable = function ($value) use (&$called) {
            $called = true;

            return $value === 'valid';
        };

        $rule = new CallableRule($callable, 'test_rule');

        // Vérifier avec une valeur valide
        $result = $rule->validate('valid');
        $this->assertTrue($called);
        $this->assertTrue($result);

        // Réinitialiser et vérifier avec une valeur invalide
        $called = false;
        $result = $rule->validate('invalid');
        $this->assertTrue($called);
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function it_passes_parameters_to_callable(): void
    {
        $receivedParams = null;
        $callable = function ($value, $params) use (&$receivedParams) {
            $receivedParams = $params;

            return true;
        };

        $rule = new CallableRule($callable, 'test_rule');
        $params = ['param1' => 'value1', 'param2' => 'value2'];

        $rule->validate('any', $params);
        $this->assertSame($params, $receivedParams);
    }
}
