.PHONY: help build up down test coverage cs-check cs-fix analyse psalm infection clean

# Couleurs pour une meilleure lisibilité
COLOR_RESET=\033[0m
COLOR_INFO=\033[32m
COLOR_COMMENT=\033[33m

# Configuration
DOCKER_COMPOSE = docker-compose

## Afficher l'aide
help:
	@echo "${COLOR_COMMENT}Available commands for PHP Validator project:${COLOR_RESET}"
	@echo "${COLOR_INFO}make build${COLOR_RESET}       - Build Docker images"
	@echo "${COLOR_INFO}make up${COLOR_RESET}          - Start Docker containers"
	@echo "${COLOR_INFO}make down${COLOR_RESET}        - Stop Docker containers"
	@echo "${COLOR_INFO}make shell${COLOR_RESET}       - Open a shell in the main container"
	@echo "${COLOR_INFO}make install${COLOR_RESET}     - Install dependencies"
	@echo "${COLOR_INFO}make test${COLOR_RESET}        - Run tests"
	@echo "${COLOR_INFO}make coverage${COLOR_RESET}    - Generate code coverage report"
	@echo "${COLOR_INFO}make cs-check${COLOR_RESET}    - Check coding standards"
	@echo "${COLOR_INFO}make cs-fix${COLOR_RESET}      - Fix coding standards"
	@echo "${COLOR_INFO}make analyse${COLOR_RESET}     - Run static analysis"
	@echo "${COLOR_INFO}make psalm${COLOR_RESET}       - Execute Psalm"
	@echo "${COLOR_INFO}make infection${COLOR_RESET}   - Run mutations tests"
	@echo "${COLOR_INFO}make clean${COLOR_RESET}       - Clean temporary files"

## Build Docker images
build:
	@echo "${COLOR_INFO}Building Docker images...${COLOR_RESET}"
	$(DOCKER_COMPOSE) build

## Start Docker containers
up:
	@echo "${COLOR_INFO}Starting Docker containers...${COLOR_RESET}"
	$(DOCKER_COMPOSE) up -d

## Stop Docker containers
down:
	@echo "${COLOR_INFO}Stopping Docker containers...${COLOR_RESET}"
	$(DOCKER_COMPOSE) down

## Open a shell in the main container
shell:
	@echo "${COLOR_INFO}Opening a shell in the main container...${COLOR_RESET}"
	$(DOCKER_COMPOSE) exec php bash

## Install dependencies
install:
	@echo "${COLOR_INFO}Installing dependencies...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php composer install

## Run tests
test:
	@echo "${COLOR_INFO}Running tests...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php-test

## Generate code coverage report
coverage:
	@echo "${COLOR_INFO}Generating code coverage report...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php-test ./vendor/bin/phpunit --coverage-html coverage

## Check coding standards
cs-check:
	@echo "${COLOR_INFO}Checking coding standards...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php-cs ./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --dry-run --diff --verbose

## Fix coding standards
cs-fix:
	@echo "${COLOR_INFO}Fixing coding standards...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php-cs ./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --verbose

## Run static analysis
analyse:
	@echo "${COLOR_INFO}Running static analysis avec PHPStan...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php-analyse ./vendor/bin/phpstan analyse src tests --configuration=phpstan.neon

## Exécuter Psalm
psalm:
	@echo "${COLOR_INFO}Run Psalm...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php bash -c "if [ ! -f psalm.xml ]; then ./vendor/bin/psalm --init src/ 4; fi && ./vendor/bin/psalm"

## Run tests de mutation
infection:
	@echo "${COLOR_INFO}Running mutation tests with Infection...${COLOR_RESET}"
	$(DOCKER_COMPOSE) run --rm php-test bash -c "php -d memory_limit=1G ./vendor/bin/infection --threads=4 --show-mutations --no-progress"

## Clean temporary files
clean:
	@echo "${COLOR_INFO}Cleaning temporary files...${COLOR_RESET}"
	rm -rf .phpunit.cache coverage .php-cs-fixer.cache .phpunit.result.cache coverage.xml
