# Combine PHP_CodeSniffer and PHP-CS-Fixer in one Config

[![Build Status](https://img.shields.io/travis/Symplify/EasyCodingStandard/master.svg?style=flat-square)](https://travis-ci.org/Symplify/EasyCodingStandard)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/EasyCodingStandard.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/EasyCodingStandard)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard)


## Install

```bash
composer require symplify/easy-coding-standard:2.0-RC1 squizlabs/php_codesniffer:"3.0.0RC4 as 2.8.1"
```


## Usage


### 1. Create Configuration

Create a `easy-coding-standard.neon` file in your root directory.

Here you can use both [Sniffs](https://github.com/squizlabs/PHP_CodeSniffer) and [Fixers](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

**We call them *Checkers*.**

#### Start Slow, Grow Fast

I recommend starting slow with 2 checkers, so you have everything under control.

```yaml
checkers:
    # declare(strict_types=1);
    - PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer

    # proper spacing around "class" keyword, after opening brace etc.
    - PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff
```

#### Don't write Checker Classes, Make use of NEON Plugin

I didn't really type `PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer`. I'm too lazy for that. I used **fuzzy search** instead. Thanks to the awesome [NEON plugin for PHPStorm](https://plugins.jetbrains.com/plugin/7060-neon-support) by [David Matějka](http://www.matej21.cz/)

@todo - fuzzy image typing class parts


### 2. Run it in CLI

```bash
vendor/bin/easy-coding-standard check src
```

You can also use name matching:

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


@todo explain


### Fix it

```bash
vendor/bin/easy-coding-standard check src --fix
```


### Configure checker

There are also user-friendly checkers that don't force you in one direction, but allow you to setup your preferences. For example short array `[]` vs long `array()`.


Let's say you want to add short syntax checker:

```yaml
checkers:
   - PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer
```

Run it:

@error screen

Hm. It looks like it's long by default. But we are using PHP 7.1, we want modern syntax!

This fixer requires us to configure this preference.

@how and what are the properties? Here we can use Neon plugin again. Just cltr + click the class and see. Look for `$defaultConfiguration` property.

```bash
checkers:
    PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer:
        syntax: short
```

Nice and clear.

Now run again. Voilà!


### Lazy Tip #1: Group by Type

When you use this tool, you'd probably end up with 50 to 60 checkers. That's fine. But hard to navigate.

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

Sometimes checker finds an error in code that inherits from 3rd party code. You are forced to use code that doesn't comply with your standards.

Just add this checker and the file to under `skip` key in parameters section:

```bash
parameters:
    skip:
        # checkers to skip
        SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff:
            # list all the files you want to skip
            - packages/EasyCodingStandard/packages/SniffRunner/src/File/File.php
```


## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
