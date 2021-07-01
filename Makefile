.PHONY: it
it: coding-standards ## Runs the coding-standards and directory target

.PHONY: coding-standards
coding-standards: vendor ## Normalizes composer.json with ergebnis/composer-normalize and fixes code style issues with friendsofphp/php-cs-fixer
	composer normalize
	mkdir -p .build/php-cs-fixer
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --diff --verbose

.PHONY: docker-down
docker-down: ## Stops the local development environment with Docker
	docker compose --file .docker/docker-compose.yml down

.PHONY: docker-up
docker-up: vendor ## Starts the local development environment with Docker
	mkdir -p .data/mariadb
	mkdir -p .data/moodle
	docker compose --file .docker/docker-compose.yml up --build --force-recreate --remove-orphans

vendor: composer.json composer.lock
	composer validate --strict
	composer install --no-interaction --no-progress
