# matrix-moodle-plugin

This plugin provides a Moodle activity to enable integration between Moodle and Matrix. It adds:

* a new field for mxid for Moodle uers
* the ability to create a new Matrix room for a course, then invite all students of that course (or group within the course) to the room

## Release

1. Run the composer steps to get dependencies.
2. Update `version.php` numbers.
3. Zip up everything except for `.git`, `vendor/bin`, `vendor/moodle`, and any IDE directories.

## Manual settings

A new custom field must be added to every user, and populated externally. To do this:

1. Go to `Site administration -> Users -> User profile fields`
2. At the bottom of the page, select `Text Input` for the new field type.
3. Enter `matrix_user_id` for the short name (**important**: this must be named *exactly* like it is here)
4. Lock the field to prevent the user modifying it, and make it unique
5. Customize the field however else you would like (name, description, visible, etc)
6. Create/save the field details

Some other external system will need to populate the field with data, or manually have it entered.
