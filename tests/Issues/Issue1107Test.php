<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Issues;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class Issue1107Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestWrongToFixedFile(__DIR__ . '/wrong/wrong1107.php.inc', __DIR__ . '/fixed/fixed1107.php.inc');
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config1107.yml';
    }
}
