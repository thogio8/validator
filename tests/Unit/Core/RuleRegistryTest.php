<?php

namespace Tests\Unit\Core;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ValidatorPro\Contracts\RuleInterface;
use ValidatorPro\Core\RuleRegistry;

/**
 * Unit tests for the RuleRegistry class.
 *
 * These tests verify the behavior of the RuleRegistry class,
 * ensuring that rule registration, retrieval, and management
 * work correctly under various scenarios.
 */
class RuleRegistryTest extends TestCase
{
    /**
     * Checks that the registry can be created empty.
     *
     * @test
     */
    public function it_can_be_instantiated_with_no_rules(): void
    {
        $mockRuleRegistry = new RuleRegistry();

        $this->assertEquals([], $mockRuleRegistry->all());
    }

    /**
     * Checks that the registry can be created with initial rules.
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
     * Checks that the constructor accepts objects implementing RuleInterface.
     *
     * @test
     */
    public function it_accepts_correctly_typed_rules_in_constructor(): void
    {
        // Create multiple RuleInterface mocks
        $firstRule = $this->createMock(RuleInterface::class);
        $secondRule = $this->createMock(RuleInterface::class);

        // Create an array associating names with these rules
        $rules = [
            'first_rule' => $firstRule,
            'second_rule' => $secondRule,
        ];

        // Create a RuleRegistry instance with this array
        $registry = new RuleRegistry($rules);

        // Check that all rules are present
        $this->assertTrue($registry->has('first_rule'));
        $this->assertTrue($registry->has('second_rule'));

        // Check that get() returns the expected instances
        $this->assertSame($firstRule, $registry->get('first_rule'));
        $this->assertSame($secondRule, $registry->get('second_rule'));

        // Check that all() returns the complete array
        $allRules = $registry->all();
        $this->assertCount(2, $allRules);
        $this->assertArrayHasKey('first_rule', $allRules);
        $this->assertArrayHasKey('second_rule', $allRules);
    }

    /**
     * Checks rule registration.
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
     * Checks that the register method returns the current instance (for chaining).
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
     * Checks that the appropriate exception is thrown with an empty name.
     *
     * @test
     */
    public function it_throws_exception_when_registering_rule_with_empty_name(): void
    {
        $ruleRegistry = new RuleRegistry();

        $mockRule = $this->createMock(RuleInterface::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Rule name cannot be empty');

        $ruleRegistry->register('', $mockRule);
    }

    /**
     * Checks that registering a rule with an existing name replaces the old rule.
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
     * Checks the retrieval of a registered rule.
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
     * Checks that null is returned for a non-existent rule.
     *
     * @test
     */
    public function it_returns_null_for_nonexistent_rule(): void
    {
        $ruleRegistry = new RuleRegistry();

        $this->assertNull($ruleRegistry->get('nonexistent_rule'));
    }

    /**
     * Checks that the retrieved instance is the one that was registered.
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
     * Checks that has() returns true for an existing rule.
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
     * Checks that has() returns false for a non-existent rule.
     *
     * @test
     */
    public function it_returns_false_for_nonexistent_rule(): void
    {
        $ruleRegistry = new RuleRegistry();

        $this->assertFalse($ruleRegistry->has('nonexistent_rule'));
    }

    /**
     * Checks that has() returns true if and only if get() returns a non-null value.
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
     * Checks that all() returns an empty array for an empty registry.
     *
     * @test
     */
    public function it_returns_empty_array_for_empty_registry(): void
    {
        $ruleRegistry = new RuleRegistry();

        $this->assertEquals([], $ruleRegistry->all());
    }

    /**
     * Checks that all() returns all registered rules.
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
     * Checks that the array returned by all() has rule names as keys.
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
     * Checks that registered rules persist between different method calls.
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
     * Checks that the returned types correspond to the expected ones.
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
     * Checks that rule names are case-sensitive.
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
