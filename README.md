# Enjoy Coding Standards with 0-knowledge of PHP_CodeSniffer nor PHP-CS-Fixer

[![Build Status *nix](https://img.shields.io/travis/Symplify/EasyCodingStandard.svg?style=flat-square)](https://travis-ci.org/Symplify/EasyCodingStandard)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/EasyCodingStandard.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/EasyCodingStandard)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard)


## Install

Add to your `composer.json`:

```json
{
    "require": {
        "symplify/easy-coding-standard": "^1.4",
        "squizlabs/php_codesniffer": "3.0.0RC4 as 2.8.1"
    }
}
```

and update:

```bash
composer update
```


## Usage


### 1. Create Configuration

Create a `easy-coding-standard.neon` file in your root directory.

Here you can use 2 types of classes:

- *Sniffs* from [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
- and *Fixers* from [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) (from Symfony world)

**We call both of these by group name *Checkers*.**

#### Start Slow, Grow Fast

I recommend starting slow, instead bumping into 20-30 checkers at once. It's more natural and you have everything under control.

Let's start with 2 checkers:

```yaml
checkers:
    # declare(strict_types=1);
    - PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer

    # proper spacing around "class" keyword, after opening brace etc.
    - PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff
```

### Don't write Checkers Classes, Make use of NEON Plugin

I didn't really typed `PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer`. I'm too lazy for that. I used **fuzzy search** instead. Thanks to awesome [NEON plugin for PHPStorm](https://plugins.jetbrains.com/plugin/7060-neon-support) by [David Matějka](http://www.matej21.cz/)

@todo - fuzzy image typing class parts


### 2. Run it!

Now run CLI:

```bash
vendor/bin/easy-coding-standard check src
```

You can use also name matching:

```bash
vendor/bin/easy-coding-standard check src/Doctrine*
```

Or multiple sources:

```bash
vendor/bin/easy-coding-standard check src/Repository src/Entity
```


Ok, we get output:

@todo: image gif



Green = good error
Red = bad error


@todo expalin


### Fix it

```bash
vendor/bin/easy-coding-standard check src --fix
```


### Configure checker

There are also user-friendly checkers, that doesn't force you in one directory, but allows you to setup your preferences. For example short array `[]` vs long `array()`.


Let's say you want to add short syntax checker:

```yaml
checkers:
   - PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer
```

Run it:

@error screen

Hm. It looks like it's long by default. But we are PHP 7.1, we want modern syntax!

This fixer requires to setup.

@how and what are the properties? Here we can use Neon plugin again. Just cltr + click the class and see. Look for `$defaultConfiguration` property.

```bash
checkers:
    PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer:
        syntax: short
```

Nice and clear.

Now run again. Voilá!


### Lazy Tip #1: Group by Type

When you use this tool, you'd probably end up with 50 to 60 checkers. That's fine. But hard to orientate.

You can group checkers by type:

```bash
checkers:
    # php 7 features
    ...

    # php syntax
    ...

    # classes
    ...


### Lazy Tip #2: Ignore What You Can't Fix

Sometimes checker founds an error on code, that inherits from 3rd party code. You are forced to use code, that doesn't comply with your standards.

Just add this checker and the file to `skip` section:

```bash
skip:
    # checkers to skip
    SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff:
        # list all the files you want to skip
        - packages/EasyCodingStandard/packages/SniffRunner/src/File/File.php
```


## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
