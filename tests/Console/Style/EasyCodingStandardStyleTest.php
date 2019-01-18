<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Style;

use PhpCsFixer\Fixer\Basic\BracesFixer;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorFactory;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class EasyCodingStandardStyleTest extends AbstractContainerAwareTestCase
{
    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var ErrorFactory
     */
    private $errorFactory;

    protected function setUp(): void
    {
        $this->easyCodingStandardStyle = $this->container->get(EasyCodingStandardStyle::class);
        $this->errorFactory = $this->container->get(ErrorFactory::class);
    }

    public function testBuildFileTableRowsFromErrors(): void
    {
        $errors = [];

        $fileInfo = new SmartFileInfo(__DIR__ . '/EasyCodingStandardStyleSource/SomeFile.php');

        $errors[] = $this->errorFactory->create(5, 'message', BracesFixer::class, $fileInfo);
        $errors[] = $this->errorFactory->create(100, 'message', BracesFixer::class, $fileInfo);

        $errorRows = $this->easyCodingStandardStyle->buildFileTableRowsFromErrors($errors);
        $this->assertCount(2, $errorRows);

        $fixableErrorRow = $errorRows[0];
        $this->assertSame([
            'line' => '5',
            'message' => 'message' . PHP_EOL . '(PhpCsFixer\Fixer\Basic\BracesFixer)',
        ], $fixableErrorRow);

        $unfixableErrorRow = $errorRows[1];
        $this->assertSame([
            'line' => '100',
            'message' => 'message' . PHP_EOL . '(PhpCsFixer\Fixer\Basic\BracesFixer)',
        ], $unfixableErrorRow);
    }
}
