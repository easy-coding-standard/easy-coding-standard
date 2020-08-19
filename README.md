# The Easiest Way to Use Any Coding Standard

[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard/stats)

![ECS-Run](docs/run-and-fix.gif)

**Used by:**

<p align="center">
    <a href="https://github.com/lmc-eu/php-coding-standard"><img src="/docs/logos/lmc.png"></a>
    <img src="/docs/logos/space.png">
    <a href="https://github.com/nette/coding-standard"><img src="/docs/logos/nette.png"></a>
    <br>
    <a href="https://github.com/shopsys/coding-standards"><img src="/docs/logos/shopsys.png"></a>
    <img src="/docs/logos/space.png">
    <a href="https://github.com/SyliusLabs/CodingStandard"><img src="/docs/logos/sylius.png"></a>
</p>

## Features

- Use [PHP_CodeSniffer || PHP-CS-Fixer](https://www.tomasvotruba.com/blog/2017/05/03/combine-power-of-php-code-sniffer-and-php-cs-fixer-in-3-lines/) - anything you like
- **2nd run under few seconds** with caching
- [Skipping files](#ignore-what-you-cant-fix) for specific checkers
- [Prepared checker sets](#use-prepared-checker-sets) - PSR12, Symfony, Common, Symplify and more...
- Use [Prefixed version](https://github.com/symplify/easy-coding-standard-prefixed) to prevent conflicts on install

Are you already using another tool?

- [How to Migrate From PHP_CodeSniffer to EasyCodingStandard in 7 Steps](https://www.tomasvotruba.com/blog/2018/06/04/how-to-migrate-from-php-code-sniffer-to-easy-coding-standard/#comment-4086561141)
- [How to Migrate From PHP CS Fixer to EasyCodingStandard in 6 Steps](https://www.tomasvotruba.com/blog/2018/06/07/how-to-migrate-from-php-cs-fixer-to-easy-coding-standard/)

## Install

```bash
composer require symplify/easy-coding-standard --dev
```

### Prefixed Version

Head over to the ["Easy Coding Standard Prefixed" repository](https://github.com/symplify/easy-coding-standard-prefixed) for more information.

## Usage

### 1. Create Configuration and Setup Checkers

Create an `ecs.php` in your root directory and add [Sniffs](https://github.com/squizlabs/PHP_CodeSniffer) or [Fixers](https://github.com/FriendsOfPHP/PHP-CS-Fixer) you'd love to use.

Let's start with some simple one... like `array()` => `[]`:

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [[
            'syntax' => 'short',
        ]]);
};
```

### 2. Run in CLI

```bash
# dry
vendor/bin/ecs check src

# fix
vendor/bin/ecs check src --fix
```

## Features

### Use Prepared Checker Sets

There are prepared sets in [`/config/set` directory](config/set) that you can use:

- [psr12](config/set/psr12.php)
- [common](config/set/common.php)
- [clean-code](config/set/clean-code.php)
- [php71](config/set/php71.php)
- ...

How to load one of them in CLI?

```bash
vendor/bin/ecs check src --set clean-code
```

How to load own config?

```bash
vendor/bin/ecs check src --config another-config.php
```

You can use all options above. Still, the best practise is to use local `ecs.php` config, as your set will grow and compose of many rules eventually:

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Configuration\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::SETS, [
        SetList::CLEAN_CODE,
        SetList::PSR_12,
    ]);
};
```

### Set Paths

You have 2 options to set paths you want to analyse.

1. In CLI:

```bash
vendor/bin/ecs check src
```

2. In `paths` parameter in `ecs.php`:

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Configuration\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);
};
```

The CLI has higher priority than parameter, so if you use CLI argument, the `sets` parameter will be ignored.

### Include Checkers Only for Some Paths

Do you want to run your rule only on new code? Limit it with `only` parameter:

```php
<?php

// ecs.php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Configuration\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(ArraySyntaxFixer::class);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::ONLY, [
        ArraySyntaxFixer::class => [
            __DIR__ . '/src/NewCode'
        ]
    ]);
};
```

### Ignore What You Can't Fix

Sometimes, checker finds an error in code that inherits from code you can't change.

No worries! Just **skip checker for this file**:

```php
<?php

// ecs.php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting\VariableCommentSniff;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Configuration\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::SKIP, [
        ArraySyntaxFixer::class => [
            # path to file (you can copy this from error report)
            __DIR__ . '/packages/EasyCodingStandard/packages/SniffRunner/src/File/File.php',
            # or multiple files by path to match against "fnmatch()"
            __DIR__ . '/packages/*/src/Command'
        ],

        // skip rule compeltely
        IsNullFixer::class => null,

        // just single one part of the rule?
        VariableCommentSniff::class . '.SomeSingleOption' => null,

        // ignore specific error message
        'Cognitive complexity for method "addAction" is 13 but has to be less than or equal to 8.' => null,
    ]);
};
```

## How to Exclude File Paths?

```php
<?php

// ecs.php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Configuration\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(ArraySyntaxFixer::class);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::EXCLUDE_PATHS, [
        __DIR__ . '/lib/PhpParser/Parser',
        __DIR__ . '/packakges/*/src/Command',
    ]);
};
```

### Do you need to Include other than `*.php` files?

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Configuration\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::FILE_EXTENSIONS, ['php', 'phpt']);
};
```

### FAQ

#### How can I see all loaded checkers?

```bash
vendor/bin/ecs show
vendor/bin/ecs show --config ...
```

#### How do I clear cache?

```bash
vendor/bin/ecs check src --clear-cache
```

#### How can I configure Cache?

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Configuration\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // default: sys_get_temp_dir() . '/_changed_files_detector_tests'
    $parameters->set(Option::CACHE_DIRECTORY, ['.ecs_cache']);

    // default: Strings::webalize(getcwd())'
    $parameters->set(Option::CACHE_NAMESPACE, 'my_project_namespace');
};
```

This comes in handy when you want to apply ECS caching mechanism on your Gitlab CI, where `getcwd()` may not always produce same cache key. E.g. `getcwd()` on Gitlab CI:

- /builds/0956d275/0/sites/my_project
- /builds/0956d275/1/sites/my_project

#### Can I use tabs, 2 spaces or "\r\n" line endings?

I'm glad you ask. Yes you can:

```php
<?php

// ecs.php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Configuration\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // default: spaces
    $parameters->set(Option::INDENTATION, 'tab');

    // default: PHP_EOL; other option "\n"
    $parameters->set(Option::LINE_ENDING, "\r\n");
};
```

## Your IDE Integration

### PHPStorm

EasyCodingStandard can be used as an External Tool

![PHPStorm Configuration](docs/phpstorm-config.png)

Go to `Preferences` > `Tools` > `External Tools` and click `+` to add a new tool.

- Name: `ecs` (Can be any value)
- Description: `easyCodingStandard` (Can be any value)
- Program: `$ProjectFileDir$/vendor/bin/ecs` (Path to `ecs` executable; On Windows path separators must be a `\`)
- Parameters: `check $FilePathRelativeToProjectRoot$` (append `--fix` to auto-fix)
- Working directory: `$ProjectFileDir$`

Press `Cmd/Ctrl` + `Shift` + `A` (Find Action), search for `ecs`, and then hit Enter. It will run `ecs` for the current file.

To run `ecs` on a directory, right click on a folder in the project browser go to external tools and select `ecs`.

You can also create a keyboard shortcut in [Preferences > Keymap](https://www.jetbrains.com/help/webstorm/configuring-keyboard-and-mouse-shortcuts.html) to run `ecs`.

### Visual Studio Code

[EasyCodingStandard for Visual Studio Code](https://marketplace.visualstudio.com/items?itemName=azdanov.vscode-easy-coding-standard) extension adds support for running EasyCodingStandard inside the editor.

## Tool Integration
| Tool | Extension | Description |
| ---- | --------- | ----------- |
| [GrumPHP](https://github.com/phpro/grumphp) | [ECS Task](https://github.com/phpro/grumphp/blob/master/doc/tasks/ecs.md) | Provides a new task for GrumPHP which runs ECS |

## Contributing

Send [issue](https://github.com/symplify/symplify/issues) or [pull-request](https://github.com/symplify/symplify/pulls) to main repository.
