<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Core\ValidationResult;

/**
 * Unit tests for the ValidationResult class.
 *
 * These tests verify that the ValidationResult correctly stores,
 * manages and exposes validation errors and data.
 */
class ValidationResultTest extends TestCase
{
    /**
     * Tests that a new instance without errors passes validation.
     *
     * @test
     * @return void
     */
    public function it_passes_when_no_errors(): void
    {
        $result = new ValidationResult();
        $this->assertTrue($result->passes());
        $this->assertFalse($result->fails());
    }

    /**
     * Tests that a new instance with errors fails validation.
     *
     * @test
     * @return void
     */
    public function it_fails_when_has_errors(): void
    {
        $result = new ValidationResult(['field' => ['Error message']]);
        $this->assertTrue($result->fails());
        $this->assertFalse($result->passes());
    }

    /**
     * Tests getting all errors.
     *
     * @test
     * @return void
     */
    public function it_returns_all_errors(): void
    {
        $errors = [
            'field1' => ['Error 1', 'Error 2'],
            'field2' => ['Error 3'],
        ];
        $result = new ValidationResult($errors);
        $this->assertEquals($errors, $result->getErrors());
    }

    /**
     * Tests getting valid data.
     *
     * @test
     * @return void
     */
    public function it_returns_valid_data(): void
    {
        $validData = ['field1' => 'value1', 'field2' => 'value2'];
        $result = new ValidationResult([], $validData);
        $this->assertEquals($validData, $result->getValidData());
    }

    /**
     * Tests getting first errors for each field.
     *
     * @test
     * @return void
     */
    public function it_returns_first_errors(): void
    {
        $errors = [
            'field1' => ['Error 1', 'Error 2'],
            'field2' => ['Error 3'],
        ];
        $result = new ValidationResult($errors);
        $expected = [
            'field1' => 'Error 1',
            'field2' => 'Error 3',
        ];
        $this->assertEquals($expected, $result->getFirstErrors());
    }

    /**
     * Tests getting first error for a specific field.
     *
     * @test
     * @return void
     */
    public function it_returns_first_error_for_field(): void
    {
        $errors = [
            'field1' => ['Error 1', 'Error 2'],
            'field2' => ['Error 3'],
        ];
        $result = new ValidationResult($errors);
        $this->assertEquals('Error 1', $result->getError('field1'));
        $this->assertEquals('Error 3', $result->getError('field2'));
        $this->assertNull($result->getError('field3'));
    }

    /**
     * Tests checking if a field has errors.
     *
     * @test
     * @return void
     */
    public function it_checks_if_field_has_errors(): void
    {
        $errors = [
            'field1' => ['Error 1'],
            'field2' => [],
        ];
        $result = new ValidationResult($errors);
        $this->assertTrue($result->hasError('field1'));
        $this->assertFalse($result->hasError('field2'));
        $this->assertFalse($result->hasError('field3'));
    }

    /**
     * Tests getting all errors for a specific field.
     *
     * @test
     * @return void
     */
    public function it_returns_all_errors_for_field(): void
    {
        $errors = [
            'field1' => ['Error 1', 'Error 2'],
            'field2' => ['Error 3'],
        ];
        $result = new ValidationResult($errors);
        $this->assertEquals(['Error 1', 'Error 2'], $result->getFieldErrors('field1'));
        $this->assertEquals(['Error 3'], $result->getFieldErrors('field2'));
        $this->assertEquals([], $result->getFieldErrors('field3'));
    }

    /**
     * Tests getting the total count of all errors.
     *
     * @test
     * @return void
     */
    public function it_counts_total_errors(): void
    {
        $errors = [
            'field1' => ['Error 1', 'Error 2'],
            'field2' => ['Error 3'],
        ];
        $result = new ValidationResult($errors);
        $this->assertEquals(3, $result->getErrorCount());
    }

    /**
     * Tests adding a new error.
     *
     * @test
     * @return void
     */
    public function it_adds_new_error(): void
    {
        $result = new ValidationResult();
        $this->assertTrue($result->passes());

        $result->addError('field', 'New error');
        $this->assertTrue($result->fails());
        $this->assertEquals(['field' => ['New error']], $result->getErrors());
    }

    /**
     * Tests adding an error to an existing field.
     *
     * @test
     * @return void
     */
    public function it_adds_error_to_existing_field(): void
    {
        $result = new ValidationResult(['field' => ['Error 1']]);
        $result->addError('field', 'Error 2');

        $this->assertEquals(['field' => ['Error 1', 'Error 2']], $result->getErrors());
    }

    /**
     * Tests getting validated fields.
     *
     * @test
     * @return void
     */
    public function it_returns_validated_fields(): void
    {
        $validatedFields = ['field1', 'field2', 'field3'];
        $result = new ValidationResult([], [], $validatedFields);
        $this->assertEquals($validatedFields, $result->getValidatedFields());
    }

    /**
     * Tests that adding an error also adds the field to validated fields.
     *
     * @test
     * @return void
     */
    public function it_adds_field_to_validated_fields_when_adding_error(): void
    {
        $result = new ValidationResult();
        $this->assertEquals([], $result->getValidatedFields());

        $result->addError('field', 'Error');
        $this->assertEquals(['field'], $result->getValidatedFields());
    }

    /**
     * Tests that the fluent interface works for addError.
     *
     * @test
     * @return void
     */
    public function it_supports_fluent_interface_for_add_error(): void
    {
        $result = new ValidationResult();
        $returnValue = $result->addError('field', 'Error');

        $this->assertSame($result, $returnValue);
    }

    /**
     * Tests that getError handles empty error arrays correctly.
     *
     * Verifies that when a field has an empty array of errors,
     * getError returns null as expected.
     *
     * @test
     * @return void
     */
    public function it_handles_empty_error_arrays_correctly(): void
    {
        $result = new ValidationResult(['field' => []]);
        $this->assertNull($result->getError('field'));
    }

    /**
     * @test
     */
    public function add_valid_data_adds_field_to_valid_data(): void
    {
        $result = new ValidationResult();

        $this->assertEmpty($result->getValidData());

        $result->addValidData('field', 'value');

        $this->assertNotEmpty($result->getValidData());
        $this->assertEquals(['field' => 'value'], $result->getValidData());
    }

    /**
     * @test
     */
    public function add_valid_data_overwrites_existing_field(): void
    {
        $result = new ValidationResult();

        $result->addValidData('field', 'value1');
        $this->assertEquals('value1', $result->getValidData()['field']);

        $result->addValidData('field', 'value2');
        $this->assertEquals('value2', $result->getValidData()['field']);
    }

    /**
     * @test
     */
    public function add_valid_data_preserves_other_fields(): void
    {
        $result = new ValidationResult();

        $result->addValidData('field1', 'value1');
        $result->addValidData('field2', 'value2');

        $expected = [
            'field1' => 'value1',
            'field2' => 'value2',
        ];

        $this->assertEquals($expected, $result->getValidData());
    }

    /**
     * @test
     */
    public function add_valid_data_returns_self_for_chaining(): void
    {
        $result = new ValidationResult();

        $returnValue = $result->addValidData('field', 'value');

        $this->assertSame($result, $returnValue);
    }

    /**
     * @test
     */
    public function add_valid_data_accepts_various_data_types(): void
    {
        $result = new ValidationResult();

        // Test avec différents types de données
        $result->addValidData('string', 'value');
        $result->addValidData('integer', 42);
        $result->addValidData('float', 3.14);
        $result->addValidData('boolean', true);
        $result->addValidData('array', ['key' => 'value']);
        $result->addValidData('null', null);
        $result->addValidData('object', new \stdClass());

        $validData = $result->getValidData();

        $this->assertIsString($validData['string']);
        $this->assertIsInt($validData['integer']);
        $this->assertIsFloat($validData['float']);
        $this->assertIsBool($validData['boolean']);
        $this->assertIsArray($validData['array']);
        $this->assertNull($validData['null']);
        $this->assertIsObject($validData['object']);
    }

    /**
     * @test
     */
    public function add_valid_data_with_nested_field_structure(): void
    {
        $result = new ValidationResult();

        $nestedArray = [
            'level1' => [
                'level2' => 'nested value',
            ],
        ];

        $result->addValidData('nested', $nestedArray);

        $validData = $result->getValidData();
        $this->assertArrayHasKey('nested', $validData);
        $this->assertIsArray($validData['nested']);
        $this->assertArrayHasKey('level1', $validData['nested']);
        $this->assertEquals('nested value', $validData['nested']['level1']['level2']);
    }
}
