<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Set\Psr12;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Psr12Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            [__DIR__ . '/wrong/wrong.php.inc', __DIR__ . '/fixed/fixed.php.inc'],
            [__DIR__ . '/wrong/wrong2.php.inc', __DIR__ . '/fixed/fixed2.php.inc'],
            // yield [__DIR__ . '/wrong/wrong3.php.inc', __DIR__ . '/fixed/fixed3.php.inc']; - not covered yet
        ]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/../../../config/set/psr12.yaml';
    }
}
