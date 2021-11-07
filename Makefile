.PHONY: it
it: coding-standards static-code-analysis tests ## Runs the coding-standards, static-code-analysis, and tests target

.PHONY: code-coverage
code-coverage: ## Collects code coverage from running unit tests with phpunit/phpunit
	mkdir -p .build/phpunit
	XDEBUG_MODE=coverage vendor/bin/phpunit --configuration=test/unit/phpunit.xml --coverage-text

.PHONY: coding-standards
coding-standards: vendor ## Normalizes composer.json with ergebnis/composer-normalize and fixes code style issues with friendsofphp/php-cs-fixer
	composer normalize
	mkdir -p .build/php-cs-fixer
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --diff --verbose

.PHONY: docker-build
docker-build: ## Builds Dockerfiles
	docker build --platform=linux/amd64 --tag gitlab-registry.matrix.org/new-vector/moodle-mod_matrix/mariadb-dev:latest .docker/mariadb/
	docker build --platform=linux/amd64 --tag gitlab-registry.matrix.org/new-vector/moodle-mod_matrix/php-dev:latest .docker/php/

.PHONY: docker-push
docker-push: docker-build ## Pushes Dockerfiles
	docker push gitlab-registry.matrix.org/new-vector/moodle-mod_matrix/mariadb-dev:latest
	docker push gitlab-registry.matrix.org/new-vector/moodle-mod_matrix/php-dev:latest

.PHONY: docker-down
docker-down: ## Stops the local development environment with Docker
	docker compose --file .docker/docker-compose.yaml down

.PHONY: docker-up
docker-up: vendor ## Starts the local development environment with Docker
	mkdir -p .data/mariadb
	mkdir -p .data/moodle
	composer install --no-interaction --no-progress
	docker compose --file .docker/docker-compose.yaml up --build --force-recreate --remove-orphans

.PHONY: release
release: docker-down ## Compresses all files required to install mod_matrix as a ZIP file
	rm -rf .build/vendor
	mv vendor .build/vendor
	rm -rf vendor
	composer install --no-dev --no-interaction --no-progress
	zip -FSr mod_matrix.zip . -x ".build/*" ".git/*" ".data/*" ".docker/*" ".gitlab/*" ".idea/*" ".notes/*" .DS_Store .editorconfig .gitignore .php-cs-fixer.php Makefile psalm.xml psalm-baseline.xml README.md
	mv .build/vendor vendor

.PHONY: static-code-analysis
static-code-analysis: vendor ## Runs a static code analysis with vimeo/psalm
	mkdir -p .build/psalm
	vendor/bin/psalm --config=psalm.xml --clear-cache
	vendor/bin/psalm --config=psalm.xml --show-info=false --stats --threads=4

.PHONY: static-code-analysis-baseline
static-code-analysis-baseline: vendor ## Generates a baseline for static code analysis with phpstan/phpstan and vimeo/psalm
	mkdir -p .build/psalm
	vendor/bin/psalm --config=psalm.xml --clear-cache
	vendor/bin/psalm --config=psalm.xml --set-baseline=psalm-baseline.xml --threads=4

.PHONY: tests
tests: vendor ## Runs unit tests with phpunit/phpunit
	mkdir -p .build/phpunit
	vendor/bin/phpunit --configuration=test/Unit/phpunit.xml

vendor: composer.json composer.lock
	composer validate --strict
	composer install --no-interaction --no-progress
