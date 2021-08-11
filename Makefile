.PHONY: it
it: coding-standards tests ## Runs the coding-standards and tests target

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
	composer install --no-interaction --no-progress
	docker compose --file .docker/docker-compose.yml up --build --force-recreate --remove-orphans

.PHONY: release
release: docker-down ## Compresses all files required to install mod_matrix as a ZIP file
	rm -rf .build/vendor
	mv vendor .build/vendor
	rm -rf vendor
	composer install --no-dev --no-interaction --no-progress
	zip -FSr mod_matrix.zip . -x ".build/*" ".git/*" ".data/*" ".docker/*" ".gitlab/*" ".idea/*" ".notes/*" .DS_Store .editorconfig .gitignore .php-cs-fixer.php Makefile README.md
	mv .build/vendor vendor

.PHONY: tests
tests: vendor ## Runs unit tests with phpunit/phpunit
	mkdir -p .build/phpunit
	vendor/bin/phpunit --configuration=test/unit/phpunit.xml

vendor: composer.json composer.lock
	composer validate --strict
	composer install --no-interaction --no-progress
