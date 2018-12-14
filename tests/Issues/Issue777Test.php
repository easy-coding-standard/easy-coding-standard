<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Issues;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue777Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([[__DIR__ . '/wrong/wrong777.php.inc', __DIR__ . '/fixed/fixed777.php.inc']]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config777.yml';
    }
}
