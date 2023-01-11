# The Easiest Way to Use Any Coding Standard

[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard/stats)

![ECS-Run](docs/run-and-fix.gif)

## Features

- **Blazing fast [parallel run](#parallel-run)**
- Use [PHP_CodeSniffer || PHP-CS-Fixer](https://tomasvotruba.com/blog/2017/05/03/combine-power-of-php-code-sniffer-and-php-cs-fixer-in-3-lines/) - anything you like
- **2nd run under few seconds** with un-changed file cache
- Skipping files for specific checkers
- **Prepared sets** - PSR-12, arrays, use statements, spaces and more... - see `SetList` class for all
- **Prefixed version** by default to allow install without conflicts on any PHP 7.2+ project

<br>

Are you already using another tool?

- [How to Migrate from PHP_CodeSniffer](https://www.tomasvotruba.com/blog/2018/06/04/how-to-migrate-from-php-code-sniffer-to-easy-coding-standard/#comment-4086561141)
- [How to Migrate from PHP CS Fixer](https://www.tomasvotruba.com/blog/2018/06/07/how-to-migrate-from-php-cs-fixer-to-easy-coding-standard/)

<br>

## Install

```bash
composer require symplify/easy-coding-standard --dev
```

## Usage

### 1. Create Configuration and Setup Checkers

- Create an `ecs.php` in your root directory

```bash
vendor/bin/ecs init
```

- Add [Sniffs](https://github.com/squizlabs/PHP_CodeSniffer)
- ...or [Fixers](https://github.com/FriendsOfPHP/PHP-CS-Fixer) you'd love to use

```php
// ecs.php
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    // A. full sets
    $ecsConfig->sets([SetList::PSR_12]);

    // B. standalone rule
    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);
};
```

### 2. Run in CLI

```bash
# dry run
vendor/bin/ecs check src

# fix
vendor/bin/ecs check src --fix
```

<br>

## Configuration

Configuration can be extended with many options. Here is list of them with example values and little description what are they for:

```php
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    // alternative to CLI arguments, easier to maintain and extend
    $ecsConfig->paths([__DIR__ . '/src', __DIR__ . '/tests']);

    // bear in mind that this will override SetList skips if one was previously imported
    // this is result of design decision in symfony https://github.com/symfony/symfony/issues/26713
    $ecsConfig->skip([
        // skip paths with legacy code
        __DIR__ . '/packages/*/src/Legacy',

        ArraySyntaxFixer::class => [
            // path to file (you can copy this from error report)
            __DIR__ . '/packages/EasyCodingStandard/packages/SniffRunner/src/File/File.php',

            // or multiple files by path to match against "fnmatch()"
            __DIR__ . '/packages/*/src/Command',

            // generics paths
            '*Sniff.php',
        ],

        // skip rule completely
        ArraySyntaxFixer::class,

        // just single one part of the rule?
        ArraySyntaxFixer::class . '.SomeSingleOption',

        // ignore specific error message
        'Cognitive complexity for method "addAction" is 13 but has to be less than or equal to 8.',
    ]);

    // scan other file extensions; [default: [php]]
    $ecsConfig->fileExtensions(['php', 'phpt']);

    // configure cache paths & namespace - useful for Gitlab CI caching, where getcwd() produces always different path
    // [default: sys_get_temp_dir() . '/_changed_files_detector_tests']
    $ecsConfig->cacheDirectory('.ecs_cache');

    // [default: \Nette\Utils\Strings::webalize(getcwd())']
    $ecsConfig->cacheNamespace('my_project_namespace');

    // indent and tabs/spaces
    // [default: spaces]
    $ecsConfig->indentation('tab');

    // [default: PHP_EOL]; other options: "\n"
    $ecsConfig->lineEnding("\r\n");
};
```

<br>

## Parallel Run

ECS runs in *X* parallel threads, where *X* is number of your threads.

Do you have 16 threads? That will speed up the process from 2,5 minutes to 10 seconds.

<br>

This process is enabled by default. To disable it, use `disableParallel()` method:

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->disableParallel();
};
```

<br>

## Coding Standards in Markdown

How to correct PHP snippets in Markdown files?

```bash
vendor/bin/ecs check-markdown README.md docs/rules.md

# to fix them, add --fix
vendor/bin/ecs check-markdown README.md docs/rules.md --fix
```

<br>

## FAQ

### How do I clear cache?

```bash
vendor/bin/ecs check src --clear-cache
```

### How to load Custom Config?

```bash
vendor/bin/ecs check src --config another-config.php
```

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Acknowledgment

The parallel run is heavily inspired by [phpstan/phpstan-src](https://github.com/phpstan/phpstan-src) by Ondřej Mirtes. Thank you.
