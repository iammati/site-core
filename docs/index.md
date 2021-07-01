# Intro to Site-Core

<a href="https://github.com/iammati/site-core" target="_blank">Site-Core</a> is an open source extension for TYPO3 which has been designed to make it easier for developers creating new Content-Elements using the Tca-Service or
kick-off new projects easier since standard methods are already implemented and shipped within site-core.

## System Requirements

* PHP 7.4
* TYPO3 v10.4

## Installation

```
composer req site/site-core
```

or using <a href="https://github.com/drud/ddev" target="_blank">DDEV-Local</a>

```
ddev composer req site/site-core
```

### Getting started

Since 1.8 of the site-core extension it's required for subpackages as (currently only) Site-Ception to maintain a `helpers.php` file inside the `DOCUMENT_ROOT` path of your webserver.

<a href="https://github.com/iammati/site-ception/#readme">Read this</a> for more information about the `helpers.php` file.

This might change in 1.9 or 2.0 since site-core will be shipped as a composer-package rather than a TYPO3 extension.
