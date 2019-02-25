<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Issues;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue1449Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/wrong1449.php.inc', __DIR__ . '/fixed/fixed1449.php.inc');
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config1449.yml';
    }
}
