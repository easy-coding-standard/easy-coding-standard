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
        return self::yieldFiles(__DIR__ . '/Fixture');
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

<br>

The `/Fixture` directory with diffs, should be a `*.php.inc` file with this content:

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
