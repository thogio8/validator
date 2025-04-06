<?php

namespace ValidatorPro\Core;

use InvalidArgumentException;
use ValidatorPro\Contracts\RuleInterface;
use ValidatorPro\Contracts\RuleRegistryInterface;

class RuleRegistry implements RuleRegistryInterface
{
    /**
     * Tableau des règles enregistrées.
     *
     * @var array<string, RuleInterface>
     */
    private array $rules = [];

    /**
     * Crée une nouvelle instance de registre de règles.
     *
     * @param array<string, RuleInterface> $rules Règles initiales à enregistrer
     */
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    /**
     * Enregistre une règle dans le registre.
     *
     * @param string $name Le nom de la règle
     * @param RuleInterface $rule L'instance de la règle
     * @return self L'instance courante pour le chaînage de méthodes
     * @throws InvalidArgumentException Si le nom de la règle est vide
     */
    public function register(string $name, RuleInterface $rule): self
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Le nom de la règle ne peut pas être vide');
        }

        $this->rules[$name] = $rule;

        return $this;
    }

    /**
     * Récupère une règle par son nom.
     *
     * @param string $name Le nom de la règle à récupérer
     * @return RuleInterface|null L'instance de la règle ou null si non trouvée
     */
    public function get(string $name): ?RuleInterface
    {
        return $this->rules[$name] ?? null;
    }

    /**
     * Vérifie si une règle existe dans le registre.
     *
     * @param string $name Le nom de la règle à vérifier
     * @return bool True si la règle existe, false sinon
     */
    public function has(string $name): bool
    {
        return isset($this->rules[$name]);
    }

    /**
     * Récupère toutes les règles disponibles dans le registre.
     *
     * @return array<string, RuleInterface> Toutes les règles indexées par leur nom
     */
    public function all(): array
    {
        return $this->rules;
    }
}
