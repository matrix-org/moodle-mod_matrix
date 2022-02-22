# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), but this project does not adhere to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

For a full diff see [`2022021500...main`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/compare/2022021500...main).

### Fixed

- Started opening chats via matrix.to when a user has a Matrix user identifier for a home server that is different than the one configured for an installation ([#83](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/83))

## [`2022021500`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/tags/2022021500)

For a full diff see [`2022020100...2022021500`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/compare/2022020100...2022021500).

### Changed

- Started creating rooms with end-to-end encryption enabled by default ([#78](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/78))
- Started creating rooms with the room history visible to all joined users ([#79](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/79))

### Fixed

- Started requiring bot power level to modify the history visibility of a room ([#80](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/80))
- Started synchronizing all rooms when a user has been updated ([#81](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/81))
- Started synchronizing all rooms when a user has been deleted ([#82](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/82))
- Started using `shared` instead of `joined` visibility for room history ([#83](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/83))

## [`2022020100`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/tags/2022020100)

For a full diff see [`2022011800...2022020100`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/compare/2022011800...2022020100).

### Added

- Started suggesting Matrix user identifiers when a user has not provided a valid Matrix user identifier yet ([#75](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/75))

### Changed

- Started displaying a form asking the user for their Matrix user identifier when they have not yet provided one ([#71](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/71))
- Started slightly validating Matrix user identifiers ([#74](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/74))

### Fixed

- Started casting identifiers from Moodle events to `int` as the documentation regarding `core\event\base` is inconsistent with actual results ([#72](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/72))
- Started casting identifiers from Moodle events to `string` instead of `int` (for consistency) ([#73](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/73))

## [`2022011800`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/tags/2022011800)

For a full diff see [`2022011100...2022011800`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/compare/2022011100...2022011800).

### Changed

- Started displaying the Matrix chat topic below the name of the chat in course view ([#62](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/62))
- Started displaying a list of chat rooms in the view when the current user is a staff user ([#70](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/70))

### Fixed

- Started disallowing staff members to change the avatar of a Matrix chat ([#63](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/63))
- Started renaming Matrix chat rooms when a staff member updates a group ([#64](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/64))
- Renamed and moved `EventSubscriber` to allow moodle to auto-load cached observers ([#65](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/65))
- Started removing all rooms for a group when a staff member removes a group ([#66](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/66))
- Stopped using `json_encode()` for room URLs ([#68](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/68))

## [`2022011101`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/tags/2022011101)

For a full diff see [`2022011100...2022011101`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/compare/2022011100...2022011101).

### Changed

- Started opening modules in a new tab ([#58](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/58))
- Started requiring the bot power level for changing room topics ([#59](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/59))

### Fixed

- Added missing translations ([#56](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/56))
- Stopped supporting `FEATURE_MOD_INTRO` to avoid notice regarding undefined `$introformat` property ([#57](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/57))
- Adjusted naming strategy for a room when a group is involved ([#60](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/60))

## [`2022011100`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/tags/2022011100)

For a full diff see [`2021120700...2022011100`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/compare/2021120700...2022011100).

### Added

- Added migration that sets the target from `matrix-to` to `element-url` for all modules when the current plugin configuration has an element URL ([#54](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/54))
- Added `CHANGELOG.md` ([#55](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/merge_requests/55))

## [`2021120700`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/tags/2021120700)

For a full diff see [`2021120600...2021120700`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/compare/2021120600...2021120700).

## [`2021120600`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/tags/2021120600)

For a full diff see [`2021112900...2021120600`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/compare/2021112900...2021120600).

## [`2021112900`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/tags/2021112900)

For a full diff see [`2021112200...2021112900`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/compare/2021112200...2021112900).

## [`2021112200`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/tags/2021112200)

For a full diff see [`2021091400...2021112200`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/compare/2021091400...2021112200).

## [`2021091400`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/tags/2021091400)

For a full diff see [`2021081000...2021091400`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/compare/2021081000...2021091400).

## [`2021081000`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/tags/2021081000)

For a full diff see [`2021070300...2021081000`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/compare/2021070300...2021081000).

## [`2021070300`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/tags/2021070300)

For a full diff see [`2021070200...2021070300`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/compare/2021070200...2021070300).

## [`2021070200`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/tags/2021070200)

For a full diff see [`3563b4c...2021070200`](https://gitlab.matrix.org/new-vector/moodle-mod_matrix/-/compare/3563b4c...2021070200).


