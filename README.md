# Combine PHP_CodeSniffer and PHP-CS-Fixer in one Config

[![Build Status](https://img.shields.io/travis/Symplify/EasyCodingStandard/master.svg?style=flat-square)](https://travis-ci.org/Symplify/EasyCodingStandard)
[![Downloads total](https://img.shields.io/packagist/dt/symplify/easy-coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/easy-coding-standard)

Includes **caching that speeds-up 2nd run to few seconds**, **skipping files for specific checkers** and checker **autocomplete**.


The easiest coding standard to start with:

```yaml
# easy-coding-standard.neon
checkers:
    - PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowShortArraySyntaxSniff
    - PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer
```


## Install

```bash
composer require symplify/easy-coding-standard
```


## Usage

### 1. Create Configuration and Setup Checkers

Create an `easy-coding-standard.neon` file in your root directory.

Here you can use 2 *checker classes*: [Sniffs](https://github.com/squizlabs/PHP_CodeSniffer) and [Fixers](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

#### Start Slow, Grow Fast

Let's start slow with 2 checkers, so we have everything under control.

```yaml
checkers:
    - PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowShortArraySyntaxSniff
    - PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer
```

#### Don't write Checker Classes, Make use of NEON Plugin

I didn't really type `PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowShortArraySyntaxSniff`. I'm too lazy for that. 

I used **class autocomplete** thanks to awesome [NEON plugin for PHPStorm](https://plugins.jetbrains.com/plugin/7060-neon-support) by [David Matejka](https://github.com/matej21/) :clap:.


![ECS-Run](docs/neon-autocomplete.gif)


### 2. Run it in CLI... and Fix It!

```bash
vendor/bin/easy-coding-standard check src
```

You can also use name matching:

```bash
vendor/bin/easy-coding-standard check src/Doctrine*
```

Or multiple sources:

```bash
vendor/bin/easy-coding-standard check src tests
```

You can also use **lazy-friendly typo-proof shortcut** `ecs`:

```bash
vendor/bin/ecs check src tests
```

#### How to Fix Things?

```bash
vendor/bin/easy-coding-standard check src --fix
```

![ECS-Run](docs/run-and-fix.gif)


#### How to Clear Cache?

To be sure your code base it checked completely, just clear the cache:

```bash
vendor/bin/easy-coding-standard check src --clear-cache
```

Cache stores all files without errors that haven't changed. It's handled by [`ChangedFilesDetector`](/packages/ChangedFilesDetector/src/ChangedFilesDetector.php)


## More Features

### Configure Your Checker

There are also user-friendly checkers that allow you to **setup YOUR preferences**.

For example short array `[]` vs long `array()`. I prefer `[]`:

```yaml
checkers:
    PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer:
        syntax: short
```

### Ignore What You Can't Fix

Sometimes, checker finds an error in code that inherits from 3rd party code, that you can't change. 

No worries! You can **skip checker for this file** in `skip` section.

```yaml
parameters:
    skip:
        # checkers to skip (you can use autocomplete here as well)
        SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff:
            # list all the files you want to skip (I usually just copy this from error report)
            - packages/EasyCodingStandard/packages/SniffRunner/src/File/File.php
```
 
### `show` Command to Display All Checkers

To see how many checkers and which exactly are loaded run:

```bash
vendor/bin/easy-coding-standard show
```

List of active checkers will be shown.

**You can also see what checkers are in another config**

```bash
vendor/bin/easy-coding-standard show --config vendor/nette/coding-standard/coding-standard-php71.neon
```

**Or explore a fixer set of PHP-CS-Fixer**:

```bash
vendor/bin/easy-coding-standard show --fixer-set Symfony
```

**Or explore or a sniff set of PHP_CodeSniffer**:

```bash
vendor/bin/easy-coding-standard show --sniff-set PSR2
```



**And print with config-like configuration** - handy for copy-pasting to your own config:

```bash
vendor/bin/easy-coding-standard show --fixer-set Symfony --with-config
```

### Pick Config in CLI

Do you want to use another config than `easy-coding-standard.neon` in your root directory? 

You can use `--config` option:

```bash
vendor/bin/easy-coding-standard show --config vendor/nette/coding-standard/general-coding-standard.neon
```  


### How to Combine More Configs

Do you have one global configuration and still want something extra for this repository? Just include the global configuration via `includes` section and add extra checkers.

```yaml
# easy-coding-standard.neon

includes:
    - vendor/nette/coding-standard/general-coding-standard.neon

checkers:
    - PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer
```


### Tabs over Spaces? We got this!

```yaml
parameters:
    indentation: tab # "spaces" by default
```

### Do you need to Include tests, `*.php`, `*.inc` or `*.phpt` files?

Normally you want to exclude these files, because they're not common code - they're just test files or dummy fixtures. In case you want to check them as well, **you can**.

Let's say you want to include `*.phpt` files.

- Create a class in `src/Finder/PhpAndPhptFilesProvider.php`
- Implement `Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface`
- Register it as services to `easy-coding-standard.neon` like any other Symfony service:

    ```yaml
    services:
        App\Finder\PhpAndPhptFilesProvider: ~
    ```

The `PhpAndPhptFilesProvider` might look like this:

```php
namespace App\Finder;

use Nette\Utils\Finder;
use SplFileInfo;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface;

final class PhpAndPhptFilesProvider implements CustomSourceProviderInterface
{
    /**
     * @param string[] $source
     */
    public function find(array $source): Finder
    {
        # $source is "source" argument passed in CLI
        # inc CLI: "vendor/bin/ecs check /src" => here: ['/src']
        return Finder::find('*.php', '*.phpt')->in($source);
    }
}
```

*Don't forget to autoload it with composer.*

**Use any Finder you like**

You can use [Nette\Finder](https://doc.nette.org/en/finder) or [Symfony\Finder](https://symfony.com/doc/current/components/finder.html).


## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
