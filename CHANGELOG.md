# Changelog

All notable changes to `helgesverre/brain` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v0.2.6] - 2026-09-27

### Added

- Support for Laravel 13 (via `orchestra/testbench` ^11 for development).

### Changed

- Allow any `openai-php/laravel` release from 0.11.0 below 1.0 (previously `^0.11.0`), so the package installs alongside current Laravel and openai-php versions.
- Point the OpenAI-compatible provider tests at models Together and Groq still serve.

## [v0.2.5] - 2025-10-08

### Added

- Support for Laravel 12 and Pest 4.

### Changed

- Require `openai-php/laravel` ^0.11.0.

## [v0.2.4] - 2024-10-29

### Changed

- Update the openai-php Laravel client and replace legacy models in tests.

## [v0.2.3] - 2024-05-08

### Added

- Change the API key and timeout on the fly.
- Documentation for Groq and other OpenAI-compatible providers.

## [v0.2.2] - 2024-03-19

### Added

- Groq support.

## [v0.2.1] - 2024-02-18

### Added

- Configurable base URL, with helpers for Perplexity, Mistral and Together AI.

## [v0.2.0] - 2024-01-26

[v0.2.6]: https://github.com/HelgeSverre/brain/compare/v0.2.5...v0.2.6
[v0.2.5]: https://github.com/HelgeSverre/brain/compare/v0.2.4...v0.2.5
[v0.2.4]: https://github.com/HelgeSverre/brain/compare/v0.2.3...v0.2.4
[v0.2.3]: https://github.com/HelgeSverre/brain/compare/v0.2.2...v0.2.3
[v0.2.2]: https://github.com/HelgeSverre/brain/compare/v0.2.1...v0.2.2
[v0.2.1]: https://github.com/HelgeSverre/brain/compare/v0.2.0...v0.2.1
[v0.2.0]: https://github.com/HelgeSverre/brain/releases/tag/v0.2.0
