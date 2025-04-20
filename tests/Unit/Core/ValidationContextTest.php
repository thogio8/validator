<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Core\ValidationContext;

/**
 * Unit tests for the ValidationContext class.
 *
 * These tests ensure that the ValidationContext properly stores and manages
 * validation rules, custom error messages, and context attributes.
 */
class ValidationContextTest extends TestCase
{
    /**
     * @var ValidationContext
     */
    private ValidationContext $context;

    /**
     * Set up a fresh context before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->context = new ValidationContext('test_context');
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_with_name_only(): void
    {
        $context = new ValidationContext('test_context');

        $this->assertEquals('test_context', $context->getName());
        $this->assertEmpty($context->getRules());
        $this->assertEmpty($context->getMessages());
        $this->assertEmpty($context->getAttributes());
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_with_all_parameters(): void
    {
        $rules = ['field' => 'required'];
        $messages = ['field.required' => 'Field is required'];
        $attributes = ['group' => 'user'];

        $context = new ValidationContext('test_context', $rules, $messages, $attributes);

        $this->assertEquals('test_context', $context->getName());
        $this->assertEquals($rules, $context->getRules());
        $this->assertEquals($messages, $context->getMessages());
        $this->assertEquals($attributes, $context->getAttributes());
    }

    /**
     * @test
     */
    public function get_name_returns_context_name(): void
    {
        $this->assertEquals('test_context', $this->context->getName());
    }

    /**
     * @test
     */
    public function get_rules_returns_all_rules(): void
    {
        $rules = ['field1' => 'required', 'field2' => 'email'];
        $context = new ValidationContext('test_context', $rules);

        $this->assertEquals($rules, $context->getRules());
    }

    /**
     * @test
     */
    public function get_messages_returns_all_messages(): void
    {
        $messages = ['field.required' => 'Custom message'];
        $context = new ValidationContext('test_context', [], $messages);

        $this->assertEquals($messages, $context->getMessages());
    }

    /**
     * @test
     */
    public function get_attributes_returns_all_attributes(): void
    {
        $attributes = ['key' => 'value'];
        $context = new ValidationContext('test_context', [], [], $attributes);

        $this->assertEquals($attributes, $context->getAttributes());
    }

    /**
     * @test
     */
    public function get_attribute_returns_specific_attribute(): void
    {
        $attributes = ['key1' => 'value1', 'key2' => 'value2'];
        $context = new ValidationContext('test_context', [], [], $attributes);

        $this->assertEquals('value1', $context->getAttribute('key1'));
        $this->assertEquals('value2', $context->getAttribute('key2'));
    }

    /**
     * @test
     */
    public function get_attribute_returns_default_when_attribute_not_found(): void
    {
        $this->assertNull($this->context->getAttribute('nonexistent'));
        $this->assertEquals('default', $this->context->getAttribute('nonexistent', 'default'));
    }

    /**
     * @test
     */
    public function with_attribute_adds_new_attribute(): void
    {
        $newContext = $this->context->withAttribute('key', 'value');

        // Verify original context is unchanged
        $this->assertEmpty($this->context->getAttributes());

        // Verify new context has the attribute
        $this->assertEquals('value', $newContext->getAttribute('key'));
    }

    /**
     * @test
     */
    public function with_attribute_replaces_existing_attribute(): void
    {
        $context = new ValidationContext('test_context', [], [], ['key' => 'old_value']);
        $newContext = $context->withAttribute('key', 'new_value');

        // Verify original context is unchanged
        $this->assertEquals('old_value', $context->getAttribute('key'));

        // Verify new context has updated attribute
        $this->assertEquals('new_value', $newContext->getAttribute('key'));
    }

    /**
     * @test
     */
    public function with_rule_adds_new_rule(): void
    {
        $newContext = $this->context->withRule('field', 'required');

        // Verify original context is unchanged
        $this->assertEmpty($this->context->getRules());

        // Verify new context has the rule
        $rules = $newContext->getRules();
        $this->assertArrayHasKey('field', $rules);
        $this->assertEquals('required', $rules['field']);
    }

    /**
     * @test
     */
    public function with_rule_replaces_existing_rule(): void
    {
        $context = new ValidationContext('test_context', ['field' => 'email']);
        $newContext = $context->withRule('field', 'required|email');

        // Verify original context is unchanged
        $this->assertEquals('email', $context->getRules()['field']);

        // Verify new context has updated rule
        $this->assertEquals('required|email', $newContext->getRules()['field']);
    }

    /**
     * @test
     */
    public function with_rule_accepts_array_rules(): void
    {
        $arrayRule = ['required', 'email'];
        $newContext = $this->context->withRule('field', $arrayRule);

        $rules = $newContext->getRules();
        $this->assertArrayHasKey('field', $rules);
        $this->assertSame($arrayRule, $rules['field']);
    }

    /**
     * @test
     */
    public function with_message_adds_new_message(): void
    {
        $newContext = $this->context->withMessage('field.required', 'Custom message');

        // Verify original context is unchanged
        $this->assertEmpty($this->context->getMessages());

        // Verify new context has the message
        $messages = $newContext->getMessages();
        $this->assertArrayHasKey('field.required', $messages);
        $this->assertEquals('Custom message', $messages['field.required']);
    }

    /**
     * @test
     */
    public function with_message_replaces_existing_message(): void
    {
        $context = new ValidationContext('test_context', [], ['field.required' => 'Old message']);
        $newContext = $context->withMessage('field.required', 'New message');

        // Verify original context is unchanged
        $this->assertEquals('Old message', $context->getMessages()['field.required']);

        // Verify new context has updated message
        $this->assertEquals('New message', $newContext->getMessages()['field.required']);
    }

    /**
     * @test
     */
    public function it_maintains_immutability_when_chaining_methods(): void
    {
        $original = $this->context;

        $modified = $original
            ->withRule('email', 'required|email')
            ->withMessage('email.required', 'Email is required')
            ->withAttribute('group', 'user');

        // Verify original is unchanged
        $this->assertEmpty($original->getRules());
        $this->assertEmpty($original->getMessages());
        $this->assertEmpty($original->getAttributes());

        // Verify modified has all changes
        $this->assertEquals('required|email', $modified->getRules()['email']);
        $this->assertEquals('Email is required', $modified->getMessages()['email.required']);
        $this->assertEquals('user', $modified->getAttribute('group'));
    }

    /**
     * @test
     */
    public function it_preserves_existing_data_when_adding_new_data(): void
    {
        $context = new ValidationContext(
            'test_context',
            ['field1' => 'required'],
            ['field1.required' => 'Message 1'],
            ['attr1' => 'value1']
        );

        $newContext = $context
            ->withRule('field2', 'email')
            ->withMessage('field2.email', 'Message 2')
            ->withAttribute('attr2', 'value2');

        // Verify all original data is preserved
        $this->assertArrayHasKey('field1', $newContext->getRules());
        $this->assertArrayHasKey('field1.required', $newContext->getMessages());
        $this->assertArrayHasKey('attr1', $newContext->getAttributes());

        // Verify new data is added
        $this->assertArrayHasKey('field2', $newContext->getRules());
        $this->assertArrayHasKey('field2.email', $newContext->getMessages());
        $this->assertArrayHasKey('attr2', $newContext->getAttributes());
    }
}
