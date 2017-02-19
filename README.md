# Enjoy coding standards with 0-knowledge of PHP_CodeSniffer nor PHP-CS-Fixer

[![Build Status *nix](https://img.shields.io/travis/Symplify/EasyCodingStandard.svg?style=flat-square)](https://travis-ci.org/Symplify/EasyCodingStandard)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/EasyCodingStandard.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/EasyCodingStandard)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard)
[![Latest stable](https://img.shields.io/packagist/v/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard)


## Install

Add to `composer.json`:

```json
composer require symplify/easy-coding-standard --dev
```


## Usage


### 1. Create configuration in `easy-coding-standard.neon`

@todo

```sh
vendor/bin/easy-coding-standard src
```


### Composer hook

In case you don't want to use this tool manually for every change in the code you make, you can add pre-commit hook via `composer.json`:

```json
"scripts": {
	"post-install-cmd": [
		"Symplify\\EasyCodingStandard\\Composer\\ScriptHandler::addPhpCsToPreCommitHook"
	],
	"post-update-cmd": [
		"Symplify\\EasyCodingStandard\\Composer\\ScriptHandler::addPhpCsToPreCommitHook"
	]
}
```

**Every time you try to commit, it will check changed `.php` files only.**

It's much faster than checking whole project, running manually or wait for CI server.
