# The Easiest way to use Coding Standard

[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard/stats)

<div class="text-align: center">
<img src="https://avatars.githubusercontent.com/u/123805080?s=200&v=4" style="width: 15em; height: 15em">
</div>

<br>

## Killer Features

- Install without on **any PHP 7.2-PHP 8.3** project with any dependencies
- Blazing fast with parallel run out of the box
- Use [PHP_CodeSniffer or PHP-CS-Fixer](https://tomasvotruba.com/blog/2017/05/03/combine-power-of-php-code-sniffer-and-php-cs-fixer-in-3-lines/) - anything you like

- Use **prepared sets** to save time:

```php
$ecsConfig->sets([SetList::PSR_12]);
```

- Use **php-cs-fixer sets** from [like you're used to](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/doc/ruleSets/index.rst)

```php
$ecsConfig->dynamicSets(['@Symfony']);
```

<br>

Do you use another tool and want to migrate? Here is "how to" for [PHP_CodeSniffer](https://tomasvotruba.com/blog/2018/06/04/how-to-migrate-from-php-code-sniffer-to-easy-coding-standard) and [PHP CS Fixer](https://tomasvotruba.com/blog/2018/06/07/how-to-migrate-from-php-cs-fixer-to-easy-coding-standard).

<br>

## Install

```bash
composer require symplify/easy-coding-standard --dev
```

<br>

## Usage

### 1. First Run

```bash
vendor/bin/ecs
```

On first run, ECS will create `ecs.php` config file with directories in your project and first rule to kick off. Run again to see the diff:

```bash
vendor/bin/ecs
```

To actually fix your code style, add `--fix`:

```bash
vendor/bin/ecs --fix
```

That's it!

<br>

## Configure

Most of the time, you'll be happy with the default configuration. The most improtant part is setting up paths, checkers and in case you go for saint defaults -sets:

```php
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withRules([
        ArraySyntaxFixer::class,
    ]);
```

## @todo add `withRootFiles()`

<br>

### Skip specific files

Love the sets of rules, but want to skip single rule or some files?

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withSkip([
        // skip single rule
        ArraySyntaxFixer::class,

        // skip single rule in specific paths
        ArraySyntaxFixer::class => [
            __DIR__ . '/src/ValueObject/',
        ],

        // skip directory by absolute or * mask
        __DIR__ . '/src/Migrations',

        // skip directories by mask
        __DIR__ . '/src/*/Legacy',
    ]);
```

<br>

### Less Commonly Used Options

You probably won't use these, but they can give you more control over the internal process:

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

return ECSConfig::configure()
    // file extensions to scan
    ->withFileExtensions(['php'])

    // configure cache paths and namespace - useful e.g. Gitlab CI caching, where getcwd() produces always different path
    ->withCache(
        directory: sys_get_temp_dir() . '/_changed_files_detector_tests',
        namespace: getcwd() // normalized to directory separator
    )

    // print contents with specific indent rules
    ->withSpacing(indentation: Option::INDENTATION_SPACES, lineEnding: PHP_EOL);
```

Mentioned values are default ones.

<br>

## FAQ

### How do I clear cache?

```bash
vendor/bin/ecs --clear-cache
```

<br>

## How to Migrate from another coding standard tool?

Do you use another tool and want to migrate? It's pretty straightforward - here is "how to":

* for [PHP_CodeSniffer](https://tomasvotruba.com/blog/2018/06/04/how-to-migrate-from-php-code-sniffer-to-easy-coding-standard)
* and [PHP CS Fixer](https://tomasvotruba.com/blog/2018/06/07/how-to-migrate-from-php-cs-fixer-to-easy-coding-standard).

<br>

## Acknowledgment

The parallel run is heavily inspired by [phpstan/phpstan-src](https://github.com/phpstan/phpstan-src).
