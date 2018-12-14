<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Issues;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue1024Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            [__DIR__ . '/wrong/wrong1024.php.inc', __DIR__ . '/fixed/fixed1024.php.inc'],
            [__DIR__ . '/wrong/wrong1024_2.php.inc', __DIR__ . '/fixed/fixed1024_2.php.inc'],
        ]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config1024.yml';
    }
}
