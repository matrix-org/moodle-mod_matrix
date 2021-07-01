# matrix-moodle-plugin

This plugin provides a Moodle activity to enable integration between Moodle and Matrix. It adds:

* a new field for mxid for Moodle uers
* the ability to create a new Matrix room for a course, then invite all students of that course (or group within the course) to the room

## Release

Update the plugin version in [`version.php`](version.php):

```diff
-$plugin->version = 2020122800;
+$plugin->version = 2021070100;
```
Run

```shell
$make release
```

Share the compressed file `mod_matrix.zip`.

## Manual settings

A new custom field must be added to every user, and populated externally. To do this:

1. Go to `Site administration -> Users -> User profile fields`
2. At the bottom of the page, select `Text Input` for the new field type.
3. Enter `matrix_user_id` for the short name (**important**: this must be named *exactly* like it is here)
4. Lock the field to prevent the user modifying it, and make it unique
5. Customize the field however else you would like (name, description, visible, etc)
6. Create/save the field details

Some other external system will need to populate the field with data, or manually have it entered.

## Setting up a local development environment

### Docker

Run

```shell
$ make docker-up
```

to start a local development environment.

:bulb: This command requires [`docker compose`](https://docs.docker.com/compose/).

### Moodle

Run

```shell
$ docker ps
```

to obtain a list of the currently running containers. There should be a container with the name `docker_moodle_php_1`.

Run

```
$ docker exec -it docker_moodle_php_1 bash
```

to enter the container.

Run

```shell
$ php admin/cli/install.php
```

to run the command line installer.

#### Database

Select **MariaDB (native/mariadb)** as database driver, then use the folliwing configuration values:

| Configuration     | Value              |
|-------------------|--------------------|
| Database host     | `moodle_mariadb`   |
| Database name     | `moodle` (default) |
| Database user     | `root`             |
| Database password | `root`             |
| Tables prefix     | `mdl_` (default)   |
| Database port     | `3306`             |
