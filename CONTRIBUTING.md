# CONTRIBUTING

We are using [GitLab CI](https://docs.gitlab.com/ee/ci/) as a continuous integration system.

For details, take a look at [`.gitlab-ci.yml`](.gitlab-ci.yml)

## Code Coverage

We are using [Xdebug](https://xdebug.org) to collect code coverage from running unit tests with [`phpunit/phpunit`](https://github.com/sebastianbergmann/phpunit).

Enable Xdebug and run

```sh
$ make code-coverage
```

to collect code coverage from running unit tests.

## Coding Standards

We are using [`ergebnis/composer-normalize`](https://github.com/ergebnis/composer-normalize) to normalize `composer.json`.

We are using [`friendsofphp/php-cs-fixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer) to enforce coding standards in PHP files.

Run

```sh
$ make coding-standards
```

to automatically fix coding standard violations.

## Static Code Analysis

We are using [`vimeo/psalm`](https://github.com/vimeo/psalm) to statically analyze the code.

Run

```sh
make static-code-analysis
```

to run a static code analysis.

We are also using the baselin feature of [`vimeo/psalm`](https://psalm.dev/docs/running_psalm/dealing_with_code_issues/#using-a-baseline-file).

Run

```sh
make static-code-analysis-baseline
```

to regenerate the baseline in [`../psalm-baseline.xml`](psalm-baseline.xml).

## Tests

We are using [`phpunit/phpunit`](https://github.com/sebastianbergmann/phpunit) to drive the development.

Run

```sh
make tests
```

to run all the tests.

## Extra lazy?

Run

```sh
$ make
```

to enforce coding standards, run a static code analysis, and run tests!

## Help

:bulb: Run

```sh
$ make help
```

to display a list of available targets with corresponding descriptions.
