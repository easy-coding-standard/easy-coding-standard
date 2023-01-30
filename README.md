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

- [How to Migrate from PHP_CodeSniffer](https://tomasvotruba.com/blog/2018/06/04/how-to-migrate-from-php-code-sniffer-to-easy-coding-standard/#comment-4086561141)
- [How to Migrate from PHP CS Fixer](https://tomasvotruba.com/blog/2018/06/07/how-to-migrate-from-php-cs-fixer-to-easy-coding-standard/)

<br>

## Install

```bash
composer require symplify/easy-coding-standard --dev
```

<br>

## Usage

### 1. First Run

To start using ECS, just run it:

```bash
vendor/bin/ecs
```

It will instantly offer to create the `ecs.php` with your directories from your project.

### 2. Setup Sets and Checkers

- Add [Sniffs](https://github.com/squizlabs/PHP_CodeSniffer) or [Fixers](https://github.com/FriendsOfPHP/PHP-CS-Fixer) you'd love to use

```php
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

### 3. Run Again

```bash
vendor/bin/ecs check src
```

The runs above are *dry runs*, so you can check the code diffs, before they get applied. If you're sure, go for a fix command:

```bash
vendor/bin/ecs check src --fix
```

<br>

## Configuration

Configuration can be extended with many options. Here is list of them with example values and little description what are they for:

```php
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([__DIR__ . '/src', __DIR__ . '/tests']);

    $ecsConfig->skip([
        // skip whole rule
        ArraySyntaxFixer::class,

        // skip directory by absolute
        __DIR__ . '/packages/Migrations',

        // skip directories by mask
        __DIR__ . '/packages/*/src/Legacy',

        // skip single rule in particular paths
        LineLenghtFixer::class => [
            __DIR__ . '/packages/EasyCodingStandard/packages/SniffRunner/src/File/File.php',
            '*Sniff.php',
        ],
    ]);

    // file extensions to scan [default: [php]]
    $ecsConfig->fileExtensions(['php', 'phpt']);

    // configure cache paths & namespace - useful for Gitlab CI caching, where getcwd() produces always different path
    // [default: sys_get_temp_dir() . '/_changed_files_detector_tests']
    $ecsConfig->cacheDirectory('.ecs_cache');

    // [default: \Nette\Utils\Strings::webalize(getcwd())']
    $ecsConfig->cacheNamespace('my_project_namespace');

    // indent and tabs/spaces [default: spaces]
    $ecsConfig->indentation('tab');

    // end of line [default: PHP_EOL]; other options: "\n"
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

return function (ECSConfig $ecsConfig): void {
    $ecsConfig->disableParallel();
};
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

<br>

## Acknowledgment

The parallel run is heavily inspired by [phpstan/phpstan-src](https://github.com/phpstan/phpstan-src) by Ondřej Mirtes. Thank you.
