<?php

namespace Tests\Integration\Validator;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Core\Validator;
use ValidatorPro\Rules\Common\RequiredRule;
use ValidatorPro\Rules\String\EmailRule;

class NullableRuleIntegrationTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
        $this->validator->addRule('required', new RequiredRule());
        $this->validator->addRule('email', new EmailRule());
    }

    /**
     * @test
     */
    public function nullable_allows_null_values_to_pass(): void
    {
        // Règles: le champ email est requis, doit être un email valide, mais peut être null
        $rules = [
            'email' => 'nullable|required|email',
        ];

        // Données: email est null
        $data = [
            'email' => null,
        ];

        // Valider
        $result = $this->validator->validate($data, $rules);

        // La validation doit réussir car nullable permet aux valeurs null de passer
        $this->assertTrue($result->passes());
        $this->assertFalse($result->fails());
        $this->assertEmpty($result->getErrors());
    }

    /**
     * @test
     */
    public function non_nullable_fields_must_satisfy_all_rules(): void
    {
        // Règles: le champ est requis et doit être un email valide
        $rules = [
            'email' => 'required|email',
        ];

        // Données: email est null
        $data = [
            'email' => null,
        ];

        // Valider
        $result = $this->validator->validate($data, $rules);

        // La validation doit échouer car email est null et n'est pas nullable
        $this->assertFalse($result->passes());
        $this->assertTrue($result->fails());
        $this->assertNotEmpty($result->getErrors());
    }
}
