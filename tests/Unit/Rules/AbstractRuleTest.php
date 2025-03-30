<?php

namespace Tests\Unit\Rules;

use PHPUnit\Framework\TestCase;
use ValidatorPro\Rules\AbstractRule;

class AbstractRuleTest extends TestCase
{
    /**
     * @var AbstractRule
     */
    private $rule;

    protected function setUp(): void
    {
        // Créer une implémentation concrète de AbstractRule pour les tests
        $this->rule = new class ([]) extends AbstractRule {
            public function validate($value, array $parameters = [], array $data = []): bool
            {
                return true;
            }

            // Exposer la méthode protégée pour les tests
            public function testCamelToSnake(string $input): string
            {
                return $this->camelToSnake($input);
            }
        };
    }

    public function testGetMessageReturnsDefaultMessage(): void
    {
        $this->assertEquals('Validation failed for :attribute', $this->rule->getMessage());
    }

    public function testGetNameReturnsFormattedName(): void
    {
        // Cette méthode est déjà testée, mais assurons-nous qu'elle fonctionne correctement
        $this->assertIsString($this->rule->getName());
    }

    public function testCamelToSnakeConversion(): void
    {
        // Test de la méthode exposée
        $this->assertEquals('camel_case', $this->rule->testCamelToSnake('camelCase'));
        $this->assertEquals('camel_case_long', $this->rule->testCamelToSnake('camelCaseLong'));
        $this->assertEquals('pascal_case', $this->rule->testCamelToSnake('PascalCase'));
        $this->assertEquals('', $this->rule->testCamelToSnake(''));
    }

    public function testGetParametersReturnsEmptyArrayByDefault(): void
    {
        $rule = new class () extends AbstractRule {
            public function validate($value, array $parameters = [], array $data = []): bool
            {
                return true;
            }
        };

        $this->assertEquals([], $rule->getParameters());
    }

    public function testGetParametersReturnsInitialParameters(): void
    {
        $params = ['min' => 5, 'max' => 10];
        $rule = new class ($params) extends AbstractRule {
            public function validate($value, array $parameters = [], array $data = []): bool
            {
                return true;
            }
        };

        $this->assertEquals($params, $rule->getParameters());
    }

    public function testSetParametersUpdatesParameters(): void
    {
        $initialParams = ['min' => 5];
        $newParams = ['min' => 10, 'max' => 20];

        $rule = new class ($initialParams) extends AbstractRule {
            public function validate($value, array $parameters = [], array $data = []): bool
            {
                return true;
            }
        };

        $returnedRule = $rule->setParameters($newParams);

        // Vérifier que la méthode retourne l'instance actuelle (fluent interface)
        $this->assertSame($rule, $returnedRule);

        // Vérifier que les paramètres ont été mis à jour
        $this->assertEquals($newParams, $rule->getParameters());
    }

    public function testSetErrorMessageUpdatesMessage(): void
    {
        $newMessage = 'Custom error message';
        $returnedRule = $this->rule->setErrorMessage($newMessage);

        // Vérifier que la méthode retourne l'instance actuelle (fluent interface)
        $this->assertSame($this->rule, $returnedRule);

        // Vérifier que le message a été mis à jour
        $this->assertEquals($newMessage, $this->rule->getMessage());
    }

    public function testSetErrorMessageWithNullDoesNotChangeMessage(): void
    {
        $originalMessage = $this->rule->getMessage();
        $this->rule->setErrorMessage(null);

        // Le message ne devrait pas changer
        $this->assertEquals($originalMessage, $this->rule->getMessage());
    }

    public function testSupportsTypeReturnsTrue(): void
    {
        $this->assertTrue($this->rule->supportsType('string'));
        $this->assertTrue($this->rule->supportsType('integer'));
        $this->assertTrue($this->rule->supportsType('array'));
    }

    public function testGetDocumentationReturnsArray(): void
    {
        $documentation = $this->rule->getDocumentation();

        // Vérifier que la documentation est un tableau avec les clés attendues
        $this->assertIsArray($documentation);
        $this->assertArrayHasKey('name', $documentation);
        $this->assertArrayHasKey('description', $documentation);
        $this->assertArrayHasKey('parameters', $documentation);
        $this->assertArrayHasKey('examples', $documentation);

        // Vérifier que le nom correspond
        $this->assertEquals($this->rule->getName(), $documentation['name']);

        // Vérifier que la description contient le nom de la règle
        $this->assertStringContainsString($this->rule->getName(), $documentation['description']);
    }

    /**
     * @test
     */
    public function it_returns_correct_documentation_format(): void
    {
        $mockRule = new class () extends AbstractRule {
            public function validate($value, array $parameters = [], array $data = []): bool
            {
                return true;
            }
        };

        $documentation = $mockRule->getDocumentation();

        // Test la structure complète
        $this->assertArrayHasKey('name', $documentation);
        $this->assertArrayHasKey('description', $documentation);
        $this->assertArrayHasKey('parameters', $documentation);
        $this->assertArrayHasKey('examples', $documentation);

        // Test spécifique pour détecter les mutations dans la concaténation
        $expectedDescription = 'Documentation for ' . $mockRule->getName() . ' rule';
        $this->assertSame($expectedDescription, $documentation['description']);
    }
}
