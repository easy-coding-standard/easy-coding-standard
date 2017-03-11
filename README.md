# Enjoy coding standards with 0-knowledge of PHP_CodeSniffer nor PHP-CS-Fixer

[![Build Status *nix](https://img.shields.io/travis/Symplify/EasyCodingStandard.svg?style=flat-square)](https://travis-ci.org/Symplify/EasyCodingStandard)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/EasyCodingStandard.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/EasyCodingStandard)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard)


## Install

Add to `composer.json`:

```json
composer require symplify/easy-coding-standard --dev
```


## Usage


### 1. Create Configuration

Create a `easy-coding-standard.neon` file in your root directory.

Here you can use 2 options:

- Sniffs from [PHP_CodeSniffer]()
- and Fixers from [PHP-CS-Fixer]() (from Symfony world)

We call both of these by group name *Checkers*.

#### Start Slow, Grow Fast

I recommend starting slow, instead bumping into 20-30 checkers at once. It's more pleasent and you have everything under control.

For example:

```yaml
php-cs-fixer:
    # declare(strict_types=1);
    - PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer

php-code-sniffer:
    # proper spacing around "class" keyword, after opening brace etc.
    - PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff
```

### Don't write Checkers Classes, Make use of NEON Plugin

I didn't really typed "PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer" or even "declare_strict_types". Who would remember that (and other 100+ ones)?

Awesome [NEON plugin] to PHPStorm by [David Matejka]() helps us with fuzzy class autocomplete:

@todo - fuzzy image typing class parts


Usually 2 letters from each important word is engouht. Like "Sttyfi" in this case.

This also allows you to add your checker very easily.


### 2. Run it!

We have configuration.

Now run Easy Coding Standard via CLI:

```bash
vendor/bin/easy-coding-standard check src
# or for lazy people like me: vendor/bin/ecs
```

You can use also name matching:

```bash
vendor/bin/easy-coding-standard check src/Doctrine*
```

And multiple sources:

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
@todo --fix
```


### Configure checker

There are also user-friendly checkers, that doesn't force you in one directory, but allows you to setup your preferences. For example short array `[]` vs long `array()`.


Let's say you want to add short syntax checker:

```yaml
php-code-sniffer:
   - PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer
```

Run it:

@error screen

Hm. It looks like it's long by default. But we are PHP 7.1, we wan't modern syntax!

This fixer requires to setup.

@how and what are the properties? Here we can use Neon plugin again. Just cltr + click the class and see. Look for `$defaultConfiguration` property.

```bash
php-code-sniffer:
    PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer:
        syntax: short
```

Nice and clear.

Now run again. Voilá!



### Lazy Tip #1: One "checkers" for them all section @todo implement!

I'm programmer and I want to focus on important algorythigms, not on where to put this checker. Why am I telling you this?

Did you noticed I put Fixer into Sniff section?

```bash
php-code-sniffer:
    PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer:
        syntax: short
```

ECS thinks about it, so it would produce this message:

@image error message

Yet that could be really annoying. Moreover when this tool is bout using Sniff and Fixers together with no pain. This IS pain.

Well, you can either learn difference between fixers and sniffer and if fixer is php-cs-fixer and sniff is php-code-sniffer, even if sniffer fixer and fixer sniffers...


**Or just use "checkers" section and let this decision to computer**:

```html
checkers:
    PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer:
        syntax: short
```


### Lazy Tip #2:

When you use this tool, you'd probably end up with 50 to 60 checkers. That's fine. But hard to orientate.

See for yourself:


@todo: messy 2 ways to put sniffs together


@todo: nicely grouped in one checker section



### Lazy Tip #3: Ignore What You Can't Fix

Sometimes checker founds an error on code, that inherits from 3rd party code. You are forced to use code, that doesn't comply with your standards. Simple step:


@todo



## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.