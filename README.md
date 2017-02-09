# Use multiple coding standards with zero-knowledge of PHP_CodeSniffer nor PHP-CS-Fixer

[![Build Status *nix](https://img.shields.io/travis/Symplify/EasyCodingStandard.svg?style=flat-square)](https://travis-ci.org/Symplify/EasyCodingStandard)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/EasyCodingStandard.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/EasyCodingStandard)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/multi-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/multi-coding-standard)
[![Latest stable](https://img.shields.io/packagist/v/symplify/multi-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/multi-coding-standard)


## Install

Add to `composer.json`:

```json
{
    "require-dev": {
        "symplify/multi-coding-standard": "~1.0",
        "symfony/console": "3.2.x-dev as 3.1",
        "squizlabs/php_codesniffer": "3.0.x-dev as 2.6"
    }
}
```

Then update:

```sh
composer update
```

## Usage

```sh
vendor/bin/multi-cs src
```
