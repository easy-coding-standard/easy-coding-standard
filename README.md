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

Create an `ecs.yaml` in your root directory and add [Sniffs](https://github.com/squizlabs/PHP_CodeSniffer) or [Fixers](https://github.com/FriendsOfPHP/PHP-CS-Fixer) you'd love to use.

Let's start with the most common one - `array()` => `[]`:

```yaml
# ecs.yaml
services:
    PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer:
        syntax: short
```

### 2. Run in CLI

```bash
# dry
vendor/bin/ecs check src

# fix
vendor/bin/ecs check src --fix
```

*Tip: Do you want [autocomplete too](https://github.com/Haehnchen/idea-php-symfony2-plugin/issues/1153)? Just use Symfony Plugin*

![ECS-Run](docs/yaml-autocomplete.gif)

## Features

### Use Prepared Checker Sets

There are prepared sets in [`/config/set` directory](config/set) that you can use:

- [clean-code.yaml](config/set/clean-code.yaml)
- [common.yaml](config/set/common.yaml)
- [php71.yaml](config/set/php71.yaml)
- [psr12.yaml](config/set/psr12.yaml)
- ...

You pick config in CLI with `--config`:

```bash
vendor/bin/ecs check src --config vendor/symplify/easy-coding-standard/config/set/clean-code.yaml
```

**Too long? Try `--set` shortcut**:

```bash
vendor/bin/ecs check src --set clean-code
```

or include more of them in config:

```yaml
# ecs.yaml
parameters:
    sets:
        - 'clean-code'
        - 'psr12'
```

### Set Paths

You have 2 options to set paths you want to analyse.

1. In CLI:

```bash
vendor/bin/ecs check src
```

2. In `paths` parameter in `ecs.yaml`:

```yaml
# ecs.yaml
parameters:
    paths:
        - 'src'
        - 'tests'
```

The CLI has higher priority than parameter, so if you use CLI argument, the `sets` parameter will be ignored.

### Exclude Checkers

What if you add `symfony` set, but don't like `PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer`?

```yaml
# ecs.yaml
parameters:
    sets:
        - 'symfony'

    skip:
        PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer: ~
```

### Include Checkers Only  for Some Paths

This feature is the exact opposite of *skip*. Do you want to run your rule only on new code? Limit it with `only` parameter:

```yaml
services:
    Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff: ~

parameters:
    only:
        Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff:
            - 'src/NewCode/*'
```

### Ignore What You Can't Fix

Sometimes, checker finds an error in code that inherits from code you can't change.

No worries! Just **skip checker for this file**:

```yaml
parameters:
    skip:
        SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff:
            # relative path to file (you can copy this from error report)
            - 'packages/EasyCodingStandard/packages/SniffRunner/src/File/File.php'

            # or multiple files by path to match against "fnmatch()"
            - '*packages/CodingStandard/src/Sniffs/*/*Sniff.php'
```

You can also skip specific codes or messages that you know from PHP_CodeSniffer:

```yaml
parameters:
    skip:
        # code to skip for all files
        SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff.UselessDocComment: ~

        # same syntax is used for skipping specific sniff messages
        'Cognitive complexity for method "addAction" is 13 but has to be less than or equal to 8.': ~

        # code to skip for specific files/patterns
        SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff.MissingTraversableParameterTypeHintSpecification:
            -  '*src/Form/Type/*Type.php'
```

Or just 2 files?

```yaml
parameters:
    exclude_files:
        # generated files
        - 'lib/PhpParser/Parser/Php5.php'
        - 'lib/PhpParser/Parser/Php7.php'
        # or with fnmatch() pattern
        - '*/lib/PhpParser/Parser/Php*.php'
```

### Do you need to Include other than `*.php` files?

```yaml
# ecs.yaml
parameters:
    file_extensions:
        - 'php'
        - 'phpt'
```

### FAQ

#### How can I see all loaded checkers?

```bash
vendor/bin/ecs show
vendor/bin/ecs show --config ...
```

#### How do I find checkers by group or type?

```bash
vendor/bin/ecs find
vendor/bin/ecs find symplify # for Symplify rules
vendor/bin/ecs find array # for array-related rules
```

#### How do I clear cache?

```bash
vendor/bin/ecs check src --clear-cache
```

#### How can I change the cache directory?

```yaml
parameters:
    cache_directory: .ecs_cache # defaults to sys_get_temp_dir() . '/_changed_files_detector_tests'
```

#### How can I change the cache namespace?

```yaml
parameters:
    cache_namespace: my_project_namespace # defaults to Strings::webalize(getcwd())'
```
This comes in handy when you want to apply ecs caching mechanism on your gitlab pipelines for example, where `getcwd()` may not always produce same cache key, thus introducing side effect, where cache may not be detected correctly.

Example  `getcwd()` on gitlab CI:

- /builds/0956d275/0/sites/my_project
- /builds/0956d275/1/sites/my_project
- /builds/0956d275/2/sites/my_project

#### Can I use tabs, 2 spaces or "\r\n" line endings?

```yaml
parameters:
    indentation: "tab" # "spaces" by default, you can also use "  " (2 spaces), "    " (4 spaces) or "	" (tab)
    line_ending: "\r\n" # PHP_EOL by default; you can also use "\n"
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
