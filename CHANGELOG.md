# 2.0.1

Maintenance release; no changes to the library code.

* CI: tested against PHP 8.3 and 8.4 in addition to 5.6 - 8.2.
* CI: replaced deprecated actions (`actions/checkout@v1` -> `@v4`,
  `actions/cache@v2` -> `@v4`, `codecov/codecov-action@v2` -> `@v5`).
* CI: `fail-fast: false` so one failing PHP version no longer cancels the
  rest of the matrix, and the composer cache key is now per PHP version.
* Added the shared GitHub Actions Release workflow.

# 2.0.0

* No BC break, if you are using as a component.
* Removed `aura/installer-default` from `composer.json` used by Aura.Framework.
* Removed Aura.Di configuration files used by Aura.Framework.
* Updated directory structure to PSR-4.
* Works with PHP version 5.4 - 8.2.
* Moved Continuous Integration from Travis to GitHub Actions.