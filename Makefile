.PHONY: help build up down test coverage cs-check cs-fix analyse psalm infection clean

# Couleurs pour une meilleure lisibilité
COLOR_RESET=\033[0m
COLOR_INFO=\033[32m
COLOR_COMMENT=\033[33m

# Configuration
DOCKER_COMPOSE = docker-compose

## Afficher l'aide
help:
	@echo "${COLOR_COMMENT}Commandes disponibles pour le projet PHP Validator:${COLOR_RESET}"
	@echo "${COLOR_INFO}make build${COLOR_RESET}       - Construire les images Docker"
	@echo "${COLOR_INFO}make up${COLOR_RESET}          - Démarrer les conteneurs Docker"
	@echo "${COLOR_INFO}make down${COLOR_RESET}        - Arrêter les conteneurs Docker"
	@echo "${COLOR_INFO}make shell${COLOR_RESET}       - Ouvrir un shell dans le conteneur principal"
	@echo "${COLOR_INFO}make install${COLOR_RESET}     - Installer les dépendances"
	@echo "${COLOR_INFO}make test${COLOR_RESET}        - Exécuter les tests"
	@echo "${COLOR_INFO}make coverage${COLOR_RESET}    - Générer un rapport de couverture de code"
	@echo "${COLOR_INFO}make cs-check${COLOR_RESET}    - Vérifier les standards de codage"
	@echo "${COLOR_INFO}make cs-fix${COLOR_RESET}      - Corriger les standards de codage"
	@echo "${COLOR_INFO}make analyse${COLOR_RESET}     - Exécuter l'analyse statique"
	@echo "${COLOR_INFO}make psalm${COLOR_RESET}       - Exécuter Psalm"
	@echo "${COLOR_INFO}make infection${COLOR_RESET}   - Exécuter les tests de mutation"
	@echo "${COLOR_INFO}make clean${COLOR_RESET}       - Nettoyer les fichiers temporaires"

## Construire les images Docker
build:
	@echo "${COLOR_INFO}Construction des images Docker...${COLOR_RESET}"
	$(DOCKER_COMPOSE) build

## Démarrer les conteneurs Docker
up:
	@echo "${COLOR_INFO}Démarrage des conteneurs Docker...${COLOR_RESET}"
	$(DOCKER_COMPOSE) up -d

## Arrêter les conteneurs Docker
down:
	@echo "${COLOR_INFO}Arrêt des conteneurs Docker...${COLOR_RESET}"
	$(DOCKER_COMPOSE) down

## Ouvrir un shell dans le conteneur principal
shell:
	@echo "${COLOR_INFO}Ouverture d'un shell dans le conteneur principal...${COLOR_RESET}"
	$(DOCKER_COMPOSE) exec php bash

## Installer les dépendances
install:
	@echo "${COLOR_INFO}Installation des dépendances...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php composer install

## Exécuter les tests
test:
	@echo "${COLOR_INFO}Exécution des tests...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php-test

## Générer un rapport de couverture de code
coverage:
	@echo "${COLOR_INFO}Génération du rapport de couverture de code...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php-test ./vendor/bin/phpunit --coverage-html coverage

## Vérifier les standards de codage
cs-check:
	@echo "${COLOR_INFO}Vérification des standards de codage...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php-cs ./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --dry-run --diff --verbose

## Corriger les standards de codage
cs-fix:
	@echo "${COLOR_INFO}Correction des standards de codage...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php-cs ./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --verbose

## Exécuter l'analyse statique
analyse:
	@echo "${COLOR_INFO}Exécution de l'analyse statique avec PHPStan...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php-analyse ./vendor/bin/phpstan analyse src tests --configuration=phpstan.neon

## Exécuter Psalm
psalm:
	@echo "${COLOR_INFO}Exécution de Psalm...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php bash -c "if [ ! -f psalm.xml ]; then ./vendor/bin/psalm --init src/ 4; fi && ./vendor/bin/psalm"

## Exécuter les tests de mutation
infection:
	@echo "${COLOR_INFO}Exécution des tests de mutation avec Infection...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php-test bash -c "php -d memory_limit=1G ./vendor/bin/infection --threads=4 --show-mutations --no-progress"

## Nettoyer les fichiers temporaires
clean:
	@echo "${COLOR_INFO}Nettoyage des fichiers temporaires...${COLOR_RESET}"
	rm -rf .phpunit.cache coverage .php-cs-fixer.cache .phpunit.result.cache coverage.xml
