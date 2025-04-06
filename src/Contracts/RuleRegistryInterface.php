<?php

namespace ValidatorPro\Contracts;

use InvalidArgumentException;

/**
 * Interface pour le registre des règles de validation.
 *
 * Cette interface définit le contrat pour les objets de registre qui stockent
 * et gèrent les règles de validation disponibles pour le validateur.
 */
interface RuleRegistryInterface
{
    /**
     * Enregistre une règle dans le registre.
     *
     * @param string $name Le nom de la règle
     * @param RuleInterface $rule L'instance de la règle
     * @return self L'instance courante pour le chaînage de méthodes
     * @throws InvalidArgumentException Si le nom de la règle est vide
     */
    public function register(string $name, RuleInterface $rule): self;

    /**
     * Récupère une règle par son nom.
     *
     * @param string $name Le nom de la règle à récupérer
     * @return RuleInterface|null L'instance de la règle ou null si non trouvée
     */
    public function get(string $name): ?RuleInterface;

    /**
     * Vérifie si une règle existe dans le registre.
     *
     * @param string $name Le nom de la règle à vérifier
     * @return bool True si la règle existe, false sinon
     */
    public function has(string $name): bool;

    /**
     * Récupère toutes les règles disponibles dans le registre.
     *
     * @return array<string, RuleInterface> Toutes les règles indexées par leur nom
     */
    public function all(): array;
}
