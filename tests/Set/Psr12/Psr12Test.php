<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Set\Psr12;

use Symplify\EasyCodingStandard\Testing\AbstractCheckerTestCase;

final class Psr12Test extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestWrongToFixedFile(
            __DIR__ . '/wrong/wrong.php.inc',
            __DIR__ . '/correct/correct.php.inc'
        );
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/../../../config/psr12.yml';
    }
}
