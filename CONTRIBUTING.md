# CONTRIBUTING


We are using [GitLab CI](https://docs.gitlab.com/ee/ci/) as a continuous integration system.

For details, take a look at [`.gitlab-ci.yml`](.gitlab-ci.yml)

## Coding Standards

We are using [`ergebnis/composer-normalize`](https://github.com/ergebnis/composer-normalize) to normalize `composer.json`.

We are using [`friendsofphp/php-cs-fixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer) to enforce coding standards in PHP files.

Run

```sh
$ make coding-standards
```

to automatically fix coding standard violations.

## Extra lazy?

Run

```sh
$ make
```

to enforce coding standards!

## Help

:bulb: Run

```sh
$ make help
```

to display a list of available targets with corresponding descriptions.
