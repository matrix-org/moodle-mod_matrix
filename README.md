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

### Moodle Command Line Installer

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

Provide configuration values to finish the configuration of the plugin:

| Configuration     | Value              |
|-------------------|--------------------|
| Homeserver URL    |                    |
| Access Token      |                    |
| Element Web URL   |                    |
| No-Reply Address  |                    |

#### Add user profile field

Navigate to [**Site Administration**](http://127.0.0.1/admin/search.php). Select the [**Users**](http://127.0.0.1/admin/search.php#linkusers) tab. In the [**Accounts**](http://127.0.0.1/admin/category.php?category=accounts) section, select [**User Profile Fields**](http://127.0.0.1/user/profile/index.php).

Choose **Text Input** to create a new profile field.

| Configuration              | Value                                                    |
|----------------------------|----------------------------------------------------------|
| Short name                 | `matrix_user_id`                                         |
| Name                       | `Matrix User Id`                                         |
| Description                | A valid matrix user identifier, e.g., @user:example.org. |
| Is this field locked?      | `yes                                                     |
| Should the data be unique? | `yes                                                     |

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

## Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).
