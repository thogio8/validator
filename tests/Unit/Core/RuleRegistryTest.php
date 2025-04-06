<?php

namespace Tests\Unit\Core;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ValidatorPro\Contracts\RuleInterface;
use ValidatorPro\Core\RuleRegistry;

/**
 * Tests unitaires pour la classe RuleRegistry.
 */
class RuleRegistryTest extends TestCase
{
    /**
     * Vérifie que le registre peut être créé vide.
     *
     * @test
     */
    public function it_can_be_instantiated_with_no_rules(): void
    {
        $mockRuleRegistry = new RuleRegistry();

        $this->assertEquals([], $mockRuleRegistry->all());
    }

    /**
     * Vérifie que le registre peut être créé avec des règles initiales.
     *
     * @test
     */
    public function it_can_be_instantiated_with_rules(): void
    {
        $mockRuleInterface = $this->createMock(RuleInterface::class);

        $rules = [
            'field' => $mockRuleInterface,
        ];

        $mockRuleRegistry = new RuleRegistry($rules);

        $this->assertTrue($mockRuleRegistry->has('field'));

        $this->assertSame($mockRuleInterface, $mockRuleRegistry->get('field'));
    }

    /**
     * Vérifie que le constructeur accepte des objets qui implémentent RuleInterface.
     *
     * @test
     */
    public function it_accepts_correctly_typed_rules_in_constructor(): void
    {
        // Créer plusieurs mocks de RuleInterface
        $firstRule = $this->createMock(RuleInterface::class);
        $secondRule = $this->createMock(RuleInterface::class);

        // Créer un tableau associant des noms à ces règles
        $rules = [
            'first_rule' => $firstRule,
            'second_rule' => $secondRule,
        ];

        // Créer une instance de RuleRegistry avec ce tableau
        $registry = new RuleRegistry($rules);

        // Vérifier que toutes les règles sont bien présentes
        $this->assertTrue($registry->has('first_rule'));
        $this->assertTrue($registry->has('second_rule'));

        // Vérifier que get() retourne bien les instances attendues
        $this->assertSame($firstRule, $registry->get('first_rule'));
        $this->assertSame($secondRule, $registry->get('second_rule'));

        // Vérifier que all() retourne le tableau complet
        $allRules = $registry->all();
        $this->assertCount(2, $allRules);
        $this->assertArrayHasKey('first_rule', $allRules);
        $this->assertArrayHasKey('second_rule', $allRules);
    }

    /**
     * Vérifie l'enregistrement d'une règle.
     *
     * @test
     */
    public function it_can_register_a_rule(): void
    {
        $ruleRegistry = new RuleRegistry();
        $mockRule = $this->createMock(RuleInterface::class);

        $ruleName = 'test_rule';
        $ruleRegistry->register($ruleName, $mockRule);

        $this->assertTrue($ruleRegistry->has($ruleName));
        $this->assertSame($mockRule, $ruleRegistry->get($ruleName));
    }

    /**
     * Vérifie que la méthode register retourne l'instance courante (pour chaînage).
     *
     * @test
     */
    public function it_returns_self_when_registering_a_rule(): void
    {
        $ruleRegistry = new RuleRegistry();

        $mockRule = $this->createMock(RuleInterface::class);

        $ruleName = 'test_rule';
        $result = $ruleRegistry->register($ruleName, $mockRule);

        $this->assertSame($ruleRegistry, $result);
    }

    /**
     * Vérifie que l'exception appropriée est lancée avec un nom vide.
     *
     * @test
     */
    public function it_throws_exception_when_registering_rule_with_empty_name(): void
    {
        $ruleRegistry = new RuleRegistry();

        $mockRule = $this->createMock(RuleInterface::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom de la règle ne peut pas être vide');

        $ruleRegistry->register('', $mockRule);
    }

    /**
     * Vérifie que l'enregistrement d'une règle avec un nom existant remplace l'ancienne règle.
     *
     * @test
     */
    public function it_can_override_existing_rule(): void
    {
        $ruleRegistry = new RuleRegistry();

        $firstRule = $this->createMock(RuleInterface::class);
        $secondRule = $this->createMock(RuleInterface::class);

        $ruleName = 'test_rule';
        $ruleRegistry->register($ruleName, $firstRule);
        $ruleRegistry->register($ruleName, $secondRule);

        $this->assertTrue($ruleRegistry->has($ruleName));
        $this->assertSame($secondRule, $ruleRegistry->get($ruleName));
    }

    /**
     * Vérifie la récupération d'une règle enregistrée.
     *
     * @test
     */
    public function it_can_get_registered_rule(): void
    {
        $ruleRegistry = new RuleRegistry();

        $mockRule = $this->createMock(RuleInterface::class);

        $ruleName = 'test_rule';
        $ruleRegistry->register($ruleName, $mockRule);

        $this->assertSame($mockRule, $ruleRegistry->get($ruleName));
    }

    /**
     * Vérifie que null est retourné pour une règle inexistante.
     *
     * @test
     */
    public function it_returns_null_for_nonexistent_rule(): void
    {
        $ruleRegistry = new RuleRegistry();

        $this->assertNull($ruleRegistry->get('nonexistent_rule'));
    }

    /**
     * Vérifie que l'instance récupérée est celle qui a été enregistrée.
     *
     * @test
     */
    public function it_returns_correct_rule_instance(): void
    {
        $ruleRegistry = new RuleRegistry();

        $mockRule = $this->createMock(RuleInterface::class);

        $ruleName = 'test_rule';
        $ruleRegistry->register($ruleName, $mockRule);

        $retrievedRule = $ruleRegistry->get($ruleName);
        $this->assertSame($mockRule, $retrievedRule);
    }

    /**
     * Vérifie que has() retourne true pour une règle existante.
     *
     * @test
     */
    public function it_returns_true_for_existing_rule(): void
    {
        $ruleRegistry = new RuleRegistry();

        $mockRule = $this->createMock(RuleInterface::class);

        $ruleName = 'test_rule';
        $ruleRegistry->register($ruleName, $mockRule);

        $this->assertTrue($ruleRegistry->has($ruleName));
    }

    /**
     * Vérifie que has() retourne false pour une règle inexistante.
     *
     * @test
     */
    public function it_returns_false_for_nonexistent_rule(): void
    {
        $ruleRegistry = new RuleRegistry();

        $this->assertFalse($ruleRegistry->has('nonexistent_rule'));
    }

    /**
     * Vérifie que has() retourne true si et seulement si get() retourne une valeur non null.
     *
     * @test
     */
    public function it_has_consistent_behavior_with_get(): void
    {
        $ruleRegistry = new RuleRegistry();
        $mockRule = $this->createMock(RuleInterface::class);
        $ruleName = 'test_rule';
        $ruleRegistry->register($ruleName, $mockRule);
        $this->assertTrue($ruleRegistry->has($ruleName));
        $this->assertSame($mockRule, $ruleRegistry->get($ruleName));
        $this->assertFalse($ruleRegistry->has('nonexistent_rule'));
        $this->assertNull($ruleRegistry->get('nonexistent_rule'));
    }

    /**
     * Vérifie que all() retourne un tableau vide sur un registre vide.
     *
     * @test
     */
    public function it_returns_empty_array_for_empty_registry(): void
    {
        $ruleRegistry = new RuleRegistry();

        $this->assertEquals([], $ruleRegistry->all());
    }

    /**
     * Vérifie que all() retourne toutes les règles enregistrées.
     *
     * @test
     */
    public function it_returns_all_registered_rules(): void
    {
        $ruleRegistry = new RuleRegistry();

        $mockRule1 = $this->createMock(RuleInterface::class);
        $mockRule2 = $this->createMock(RuleInterface::class);

        $ruleRegistry->register('rule1', $mockRule1);
        $ruleRegistry->register('rule2', $mockRule2);

        $allRules = $ruleRegistry->all();

        $this->assertCount(2, $allRules);
        $this->assertArrayHasKey('rule1', $allRules);
        $this->assertArrayHasKey('rule2', $allRules);
        $this->assertSame($mockRule1, $allRules['rule1']);
        $this->assertSame($mockRule2, $allRules['rule2']);
    }

    /**
     * Vérifie que le tableau retourné par all() a les noms de règles comme clés.
     *
     * @test
     */
    public function it_returns_rules_indexed_by_name(): void
    {
        $ruleRegistry = new RuleRegistry();

        $mockRule1 = $this->createMock(RuleInterface::class);
        $mockRule2 = $this->createMock(RuleInterface::class);

        $ruleRegistry->register('rule1', $mockRule1);
        $ruleRegistry->register('rule2', $mockRule2);

        $allRules = $ruleRegistry->all();

        $this->assertCount(2, $allRules);
        $this->assertArrayHasKey('rule1', $allRules);
        $this->assertArrayHasKey('rule2', $allRules);
        $this->assertSame($mockRule1, $allRules['rule1']);
        $this->assertSame($mockRule2, $allRules['rule2']);
    }

    /**
     * Vérifie que les règles enregistrées persistent entre différents appels de méthodes.
     *
     * @test
     */
    public function it_preserves_rules_between_method_calls(): void
    {
        $ruleRegistry = new RuleRegistry();

        $mockRule = $this->createMock(RuleInterface::class);

        $ruleName = 'test_rule';
        $ruleRegistry->register($ruleName, $mockRule);

        $this->assertTrue($ruleRegistry->has($ruleName));
        $this->assertSame($mockRule, $ruleRegistry->get($ruleName));
        $this->assertCount(1, $ruleRegistry->all());

        $ruleRegistry->register('another_rule', $this->createMock(RuleInterface::class));

        $this->assertCount(2, $ruleRegistry->all());
        $this->assertTrue($ruleRegistry->has('another_rule'));
        $this->assertSame($mockRule, $ruleRegistry->get($ruleName));
    }

    /**
     * Vérifie que les types retournés correspondent à ceux attendus.
     *
     * @test
     */
    public function it_returns_correct_types(): void
    {
        $ruleRegistry = new RuleRegistry();

        $mockRule = $this->createMock(RuleInterface::class);

        $ruleName = 'test_rule';
        $ruleRegistry->register($ruleName, $mockRule);

        $this->assertIsBool($ruleRegistry->has($ruleName));
        $this->assertIsObject($ruleRegistry->get($ruleName));
        $this->assertIsArray($ruleRegistry->all());
        $this->assertIsObject($ruleRegistry->register($ruleName, $mockRule));
    }

    /**
     * Vérifie que les noms de règles sont sensibles à la casse.
     *
     * @test
     */
    public function it_handles_rule_names_case_sensitively(): void
    {
        $ruleRegistry = new RuleRegistry();

        $mockRule1 = $this->createMock(RuleInterface::class);
        $mockRule2 = $this->createMock(RuleInterface::class);

        $ruleRegistry->register('rule', $mockRule1);
        $ruleRegistry->register('Rule', $mockRule2);

        $this->assertTrue($ruleRegistry->has('rule'));
        $this->assertTrue($ruleRegistry->has('Rule'));
        $this->assertSame($mockRule1, $ruleRegistry->get('rule'));
        $this->assertSame($mockRule2, $ruleRegistry->get('Rule'));
        $this->assertCount(2, $ruleRegistry->all());
    }
}
