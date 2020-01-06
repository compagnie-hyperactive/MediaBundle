# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

#[v0.4.0](https://github.com/compagnie-hyperactive/TranslateBundle/releases/tag/v0.4.0) - 2019-05-31
### Added
- `translated_path` methods

#[v0.3.1](https://github.com/compagnie-hyperactive/TranslateBundle/releases/tag/v0.3.1) - 2019-05-27
### Fixed
- LangSwitchHelper now correctly handling routes with multiple translated paths

# [v0.3.0](https://github.com/compagnie-hyperactive/TranslateBundle/releases/tag/v0.3.0) - 2019-05-10
### Changed
- LangSwitchHelper now providing langcodes as keys in paths arrays

# [v0.2.3](https://github.com/compagnie-hyperactive/TranslateBundle/releases/tag/v0.2.3) - 2019-04-23
### Changed
- Default choice in language select is now the current locale

# [v0.2.2](https://github.com/compagnie-hyperactive/TranslateBundle/releases/tag/v0.2.2) - 2019-04-18
### Fixed
- Entity could be its own translated parent, which makes no sense

## [v0.2.1](https://github.com/compagnie-hyperactive/TranslateBundle/releases/tag/v0.2.1) – 2019-04-09
### Fixed
- Twig extension was causing fatal error in some cases

## [v0.2.0](https://github.com/compagnie-hyperactive/TranslateBundle/releases/tag/v0.2.0) – 2019-04-09
### Added
- LanguageType form
- Method in `TranslationsHelper` to check if translations are enabled
- This changelog (:
- Default value to `available_languages` param
### Fixed
- Wrong declaration for Twig extension runtime
- Wrong namespace in Twig extension runtime
- Not using string cast anymore as entity default label
- Wrong format for `available_languages` param

## [v0.1.1](https://github.com/compagnie-hyperactive/TranslateBundle/releases/tag/v0.1.1) – 2019-04-08
### Fixed
- Fix namespace naming

## [v0.1.0](https://github.com/compagnie-hyperactive/TranslateBundle/releases/tag/v0.1.0) – 2019-04-08
### Added
- Initial code import
