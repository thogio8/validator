<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Core\ValidationResult;

class ValidationResultTest extends TestCase
{
    private ValidationResult $result;

    protected function setUp(): void
    {
        $this->result = new ValidationResult();
    }

    /**
     * @test
     */
    public function it_starts_with_empty_errors(): void
    {
        $this->assertEmpty($this->result->getErrors());
        $this->assertTrue($this->result->passes());
        $this->assertFalse($this->result->fails());
    }

    /**
     * @test
     */
    public function it_adds_error_correctly(): void
    {
        $this->result->addError('email', 'Invalid email');

        $this->assertTrue($this->result->fails());
        $this->assertFalse($this->result->passes());
        $this->assertCount(1, $this->result->getErrors());
        $this->assertEquals(['email' => ['Invalid email']], $this->result->getErrors());
    }

    /**
     * @test
     */
    public function it_adds_multiple_errors_for_same_field(): void
    {
        $this->result->addError('email', 'Invalid email');
        $this->result->addError('email', 'Email already exists');

        $this->assertCount(1, $this->result->getErrors());
        $this->assertCount(2, $this->result->getFieldErrors('email'));
        $this->assertEquals(['Invalid email', 'Email already exists'], $this->result->getFieldErrors('email'));
    }

    /**
     * @test
     */
    public function it_adds_validated_field_only_once(): void
    {
        $this->result->addValidatedField('email');
        $this->result->addValidatedField('email'); // Add the same field twice

        $this->assertCount(1, $this->result->getValidatedFields());
        $this->assertEquals(['email'], $this->result->getValidatedFields());
    }

    /**
     * @test
     */
    public function it_returns_first_errors_correctly(): void
    {
        $this->result->addError('email', 'Invalid email');
        $this->result->addError('email', 'Email already exists');
        $this->result->addError('name', 'Name is required');

        $firstErrors = $this->result->getFirstErrors();
        $this->assertEquals('Invalid email', $firstErrors['email']);
        $this->assertEquals('Name is required', $firstErrors['name']);
    }

    /**
     * @test
     */
    public function it_counts_errors_correctly(): void
    {
        $this->result->addError('email', 'Invalid email');
        $this->result->addError('email', 'Email already exists');
        $this->result->addError('name', 'Name is required');

        $this->assertEquals(3, $this->result->getErrorCount());
    }
}
