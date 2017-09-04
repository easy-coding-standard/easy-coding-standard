<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Style;

use PhpCsFixer\Fixer\Basic\BracesFixer;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;

final class EasyCodingStandardStyleTest extends AbstractContainerAwareTestCase
{
    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    protected function setUp(): void
    {
        $this->easyCodingStandardStyle = $this->container->get(EasyCodingStandardStyle::class);
    }

    public function testBuildFileTableRowsFromErrors(): void
    {
        $errors = [];
        $errors[] = new Error(5, 'message', BracesFixer::class, true);
        $errors[] = new Error(100, 'message', BracesFixer::class, false);

        $errorRows = $this->easyCodingStandardStyle->buildFileTableRowsFromErrors($errors);
        $this->assertCount(2, $errorRows);

        $fixableErrorRow = $errorRows[0];
        $this->assertSame([
            'line' => '<fg=black;bg=green>5</>',
            'message' => '<fg=black;bg=green>message' . PHP_EOL . '(PhpCsFixer\Fixer\Basic\BracesFixer)</>',
        ], $fixableErrorRow);

        $unfixableErrorRow = $errorRows[1];
        $this->assertSame([
            'line' => '100',
            'message' => 'message' . PHP_EOL . '(PhpCsFixer\Fixer\Basic\BracesFixer)',
        ], $unfixableErrorRow);
    }
}
