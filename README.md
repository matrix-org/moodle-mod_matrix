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
make release
```

Share the compressed file `mod_matrix.zip`.

## Setting up a local development environment

### Docker

Run

```shell
make docker-up
```

to start a local development environment.

:bulb: This command requires [`docker compose`](https://docs.docker.com/compose/).

### Moodle Command Line Installer

Run

```shell
docker ps
```

to obtain a list of the currently running containers. There should be a container with the name `docker_moodle_php_1`.

Run

```shell
docker exec -it docker_moodle_php_1 bash
```

to enter the container.

Run

```shell
cd /var/www/html
```

to change into the root of the Moodle installation (working directory is set to the root of the Moodle plugin).

Run

```shell
php admin/cli/install.php
```

to run the command line installer.

#### Basics

| Configuration              | Value                           |
|----------------------------|---------------------------------|
| Language                   | `en` (default)                  |
| Data Directory Permissions | `2777` (default)                |
| Web Address                | your local host                 |
| Data Directory             | `/var/www/moodledata` (default) |

#### Database

| Configuration     | Value              |
|-------------------|--------------------|
| Database driver   | `mariadb`          |
| Database host     | `moodle_mariadb`   |
| Database name     | `moodle` (default) |
| Tables prefix     | `mdl_` (default)   |
| Database port     | `3306`             |
| Database user     | `root`             |
| Database password | `root`             |

#### Other

| Configuration                | Value                              |
|------------------------------|------------------------------------|
| Full site name               | `Moodle Matrix Plugin Development` |
| Short name                   | `mmpd`                             |
| Admin account user name      | `admin` (default)                  |
| New admin user password      | `password`                         |
| New admin user email address | your email address                 |
| Upgrade key                  | (default)                          |

### Moodle Administration

Navigate to [http://127.0.0.1](http://127.0.0.1) to finish setting up the installation.

Log in with the credentials for the administrator account created created when running the command line installer.

#### Install the `mod_matrix` plugin

Navigate to [http://127.0.0.1/admin/settings.php?section=modsettingmatrix](http://127.0.0.1/admin/settings.php?section=modsettingmatrix) and provide configuration values to finish the configuration of the plugin:

| Configuration     | Value              |
|-------------------|--------------------|
| Homeserver URL    |                    |
| Access Token      |                    |
| Element Web URL   |                    |
| No-Reply Address  |                    |

#### Add course

Navigate to [**Site Administration**](http://127.0.0.1/admin/search.php). Select the [**Courses**](http://127.0.0.1/admin/search.php#linkcourses) tab. In the [**Courses**](http://127.0.0.1/admin/category.php?category=courses) section, select [**Add a new course**](http://127.0.0.1/course/edit.php?category=0).

Fill in some data for a course.

| Configuration              | Value              |
|----------------------------|--------------------|
| Course full name           | `Example Course`   |
| Course short name          | `Example`          |

Click **Save and Display**.

In the **Participants** section, click **Enrol users**.

In the **Enrol users** overlay, select the previously created administrator and assign the **Teacher** role.

Click **Enrol users**.

Click **Proceed to course content**.

#### Edit course

In the course view, click **Turn editing on**. In the **Announcements** section, click on **Add an activity or resource**.

In the  **Add an activity or resource** overlay, click on **Matrix** to add a new matrix.

Click **Save and Display**.

#### Disable Recycle Bin during Development

Navigate to [**Site Administration**](http://127.0.0.1/admin/search.php). Select the [**Plugins**](http://moodle.com.localheinz/admin/category.php?category=modules) tab. Select [**Category: Admin Tools**](http://moodle.com.localheinz/admin/category.php?category=tools).

In the [**Recycle Bin**](http://moodle.com.localheinz/admin/settings.php?section=tool_recyclebin) section, deselect **Enable course recycle bin** and **Enable category recycle bin**.

Click **Save changes**.

## Changelog

Please have a look at [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).
