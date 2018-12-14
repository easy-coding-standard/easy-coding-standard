<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Issues;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue1097Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([[__DIR__ . '/wrong/wrong1097.php.inc', __DIR__ . '/fixed/fixed1097.php.inc']]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config1097.yml';
    }
}
