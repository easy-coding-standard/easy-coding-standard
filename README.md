# The Easiest way to use Coding Standard

[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard/stats)

<br>

## Killer Features

- Install on **any PHP 7.2-PHP 8.3** project with any dependencies
- Blazing fast with parallel run out of the box
- Use [PHP_CodeSniffer or PHP-CS-Fixer](https://tomasvotruba.com/blog/2017/05/03/combine-power-of-php-code-sniffer-and-php-cs-fixer-in-3-lines/) - anything you like
- Use **prepared sets**, [PHP CS Fixer sets](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/doc/ruleSets/index.rst), or [PHP Code Sniffer standards](https://github.com/PHPCSStandards/PHP_CodeSniffer/wiki/Usage#specifying-a-coding-standard) to save time

<br>

## Install

```bash
composer require symplify/easy-coding-standard --dev
```

<br>

## Usage

```bash
vendor/bin/ecs
```

On the first run, ECS creates `ecs.php` config file with directories and first rule to kick off.

Then you can run again to see the suggested diffs:

```bash
vendor/bin/ecs
```

To actually **fix your code**, add `--fix`:

```bash
vendor/bin/ecs --fix
```

That's it!

<br>

## Configure

Most of the time, you'll be happy with the default configuration. The most relevant part is configuring paths, checkers and sets:

```php
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withRules([
        ArraySyntaxFixer::class,
    ])
    // Warnings for included PHPCS rules are disabled by default,
    // but they can be enabled manually.
    ->withWarnings()
    ->withPreparedSets(psr12: true);
```

<br>

Do you want to check all `*.php` files in your root (`ecs.php`, `rector.php` etc.)? Instead of listing them one by one, use `->withRootFiles()` method:

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withRootFiles();
```

<br>

Do you want to include one of 44 sets from [php-cs-fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/blob/master/doc/ruleSets/index.rst)?

You can:

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withPhpCsFixerSets(perCS20: true, doctrineAnnotation: true);
```

<br>

Do you want to use [an existing PHPCS config or standard](https://github.com/PHPCSStandards/PHP_CodeSniffer/wiki/Usage#specifying-a-coding-standard)?
You have plenty of options:

```php
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])

    // Load your existing [.]phpcs.xml[.dist]
    ->withSnifferStandards()

    // Use any of their 8 built-ins.
    ->withSnifferStandards(psr12: true)

    // Maybe third parties interest you?
    ->withSnifferStandards(vendor: 'WordPress')

    // Or maybe you use LOTS of configs?
    ->withSnifferStandards(
        config: [ 'phpcs.xml.dist', 'phpcs.xml' ],
        vendor: [ 'WordPress', 'PHPCompatibility' ],
        psr12: true
    )

    // The standards warning severities are respected by default,
    // this returns to the default behavior of skipping most warnings.
    ->withSnifferStandards(withWarnings: false);
```

> [!IMPORTANT]
> This only supports a **subset** of PHPCS configuration:
>
> - Included rules and their default configurations.
> - Rules excluded globally or based on patterns.
> - Severity levels.
> - Ruleset inheritance.
>
> Rules included only for specific paths will be included globally instead.
> To migrate, enable these rules globally and specify _excluded_ paths instead.
>
> All other arguments, parameters, or settings are ignored.
> This includes specified file patterns and extensions.

<br>

### How to Skip Files/Rules?

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

### Less Common Options

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
    ->withSpacing(indentation: Option::INDENTATION_SPACES, lineEnding: PHP_EOL)

    // modify parallel run
    ->withParallel(timeoutSeconds: 120, maxNumberOfProcess: 32, jobSize: 20);
```

Mentioned values are default ones.

<br>

Do you use ECS across variety of project? Do you want to run them always the same way in each of those project? Let's make use of [Composer scripts](https://blog.martinhujer.cz/have-you-tried-composer-scripts/)

This command adds 2 handy scripts to your `composer.json`:

```bash
vendor/bin/ecs scripts
```

Run them always the same way - to check the code:

```bash
composer check-cs
```

To apply fixes, run:

```bash
composer fix-cs
```

<br>

### Controlling Output Format

You may want to use ECS to generate reports for third-party tooling.

We currently provide formatters for:

- `console`:  Human-oriented printing à la PHP CS Fixer.
- `json`:  A custom JSON blob for arbitrary tooling.
- `checkstyle`: Useful for Github Action Reports.
- `gitlab`: For Gitlab code quality reports or Code Climate tooling.

For information on how each of these behave, refer to their respective
[implementations](src/Console/Output/).

<br>

## FAQ

### How do I clear cache?

```bash
vendor/bin/ecs --clear-cache
```

### How can I see all used rules?

```bash
vendor/bin/ecs list-checkers
```

Do you look for json format?

```bash
vendor/bin/ecs list-checkers --output-format json
```

### ECS isn't respecting my `phpcs:ignore` directives?

Currently, we do not support comment-based directives to enable, disable, or
ignore specific rules or specific tools. We do support our own global toggle:

```php
// @codingStandardsIgnoreStart
$atom = "[-a-z0-9!#$%&'*+/=?^_`{|}~]"; // RFC 5322 unquoted characters in local-part
// @codingStandardsIgnoreEnd
```

<br>

## How to Migrate from another coding standard tool?

Do you use another tool and want to migrate? It's pretty straightforward - here is "how to":

* for [PHP_CodeSniffer](https://tomasvotruba.com/blog/2018/06/04/how-to-migrate-from-php-code-sniffer-to-easy-coding-standard)
* and [PHP CS Fixer](https://tomasvotruba.com/blog/2018/06/07/how-to-migrate-from-php-cs-fixer-to-easy-coding-standard).
