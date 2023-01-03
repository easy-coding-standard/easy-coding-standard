# How to Test Fixers and Sniffs

Do you write your own fixer and sniffs? Would you like to test them without having to learn a lot about their internals?

**This package make fixer and sniff testing with 1 single approach super easy**.

## Usage

1. Extend `Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase` class

2. Provide files to `doTestFiles()` method

```php
namespace Your\CodingStandard\Tests\Fixer\YourFixer;

use Iterator;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;
use Your\CondingStandard\Fixer\YourFixer;

final class YourFixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public function provideData(): Iterator
    {
        yield self::yieldFiles(__DIR__ . '/Fixture');
    }

    /**
     * @dataProvider provideDataWithFileErrors()
     */
    public function testFileErrors(string $filePath, int $expectedErrorCount): void
    {
        $this->doTestFileInfoWithErrorCountOf($filePath, $expectedErrorCount);
    }

    public function provideDataWithFileErrors(): Iterator
    {
        yield [__DIR__ . '/Fixture/wrong.php.inc', 1];
        yield [__DIR__ . '/Fixture/correct.php.inc', 0];
    }

    protected function getCheckerClass(): string
    {
        return YourFixer::class;
    }
}
```

Instead of `[__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc']` you can use single file: `__DIR__ . '/fixture/fixture.php.inc'` in this format:

```php
<?php

$array = array();

?>
-----
<?php

$array = [];

?>
```

In pseudo-code:

```bash
before
------
after
```

<br>

## Report Issues

In case you are experiencing a bug or want to request a new feature head over to the [Symplify monorepo issue tracker](https://github.com/symplify/symplify/issues)

## Contribute

The sources of this package are contained in the Symplify monorepo. We welcome contributions for this package on [symplify/symplify](https://github.com/symplify/symplify).
