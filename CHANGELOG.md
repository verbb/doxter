# Doxter Changelog
> Markdown Editor & Parser for [Craft CMS](http://craftcms.com)

## 3.1.5 - 2018-03-23
## Added
- Added plugins settings
- Added field settings

## 3.1.4 - 2017-07-28
## Fixed
- Fixed issue #11 where asset selector modal would only show images
- Fixed several formatting issues in `README` (Hat tip to Brandon Kelly)

## 3.1.3 - 2017-05-17
### Fixed
- Fixed issue where `value.raw` in the field, could break the dashboard

## 3.1.2 - 2017-05 [CRITICAL]-12
### Removed
- Removes dependency on `FitVids` for responsive video embed shortcode

## 3.1.1 - 2017-04 [CRITICAL]-17
### Fixed
- Fixed issue [#6](https://github.com/selvinortiz/craft-plugin-doxter/issues/6) where parsing prevented the creation of new entries

> {note} Sooner or later you'll do something stupid that seems lazy, like not test if entries can be created when it contains one of your fields. Well, this was pure stupidity and not laziness, I assure you 😂 Sorry everyone!

## 3.1.0 - 2017-04-16
### Added
- Added emoji support as requested on issue [#5](https://github.com/selvinortiz/craft-plugin-doxter/issues/5) 👍
- Added (back) support for reference tag parsing
- Added full documentation as a Wiki

### Fixed
- Fixed full viewport editing while in the Doxter field
- Fixed issue [#4](https://github.com/selvinortiz/craft-plugin-doxter/issues/4) where `entry.doxterField` would render escaped html

### Changed
- Updated codebase to adhere to Craft Coding Standards

## 3.0.8 - 2017-04-11
### Fixed
- Fixed full viewport editing from issue [#2](https://github.com/selvinortiz/craft-plugin-doxter/issues/2)

### Changed
- Updated plugin.(css|js) to use spaces
- Updated plugin.js to include semi-colons where missing

## 3.0.7 - 2017-04-02
### Fixed
- Fixed issue where `hasSettings` is no longer supported

### Changed
- Updated all dependencies to the latest stable version

## 3.0.6 - 2017-02-06
### Fixed
- Fixed critical issue where the Shortcode parser would pee himself if fields were empty

## 3.0.5 - 2017-02-06
### Added
- Added SmartyPants as a dependency for handling typography
- Added back support for **Shortcodes**
- Added back the `doxter()` global to access the public API
- Added a new `DoxterEvent` to notify about parsing events
- Updates plugin icons

## 3.0.4 - 2017-01-03
### Added
- Added prettier icons

### Updated
- Updated the way components are accessed for improved type hinting

### Fixed
- Fixed an issue where the variable class was loaded

## 3.0.3 - 2017-01-02
### Added
- Added cover image and updated icons

## 3.0.1 - 2017-01-02
### Added
- Added changelog and documentation URLs

### Fixed
- Fixed an issue where parsers were not instantiated correctly

## 3.0.0 - 2017-01-02
- Initial (beta) release for Craft 3
