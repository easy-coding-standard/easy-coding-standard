<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Error;

use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorFactory;
use Symplify\EasyCodingStandard\Error\ErrorSorter;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class ErrorSorterTest extends AbstractKernelTestCase
{
    /**
     * @var ErrorSorter
     */
    private $errorSorter;

    /**
     * @var ErrorFactory
     */
    private $errorFactory;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $this->errorSorter = self::$container->get(ErrorSorter::class);
        $this->errorFactory = self::$container->get(ErrorFactory::class);
    }

    public function test(): void
    {
        /** @var Error[][] $sortedMessages */
        $sortedMessages = $this->errorSorter->sortByFileAndLine($this->getUnsortedMessages());

        $this->assertSame(['anotherFilePath', 'filePath'], array_keys($sortedMessages));
        $this->assertSame(5, $sortedMessages['anotherFilePath'][0]->getLine());
        $this->assertSame(15, $sortedMessages['anotherFilePath'][1]->getLine());
    }

    /**
     * @return Error[][]
     */
    private function getUnsortedMessages(): array
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/ErrorSorterSource/SomeFile.php');

        $firstError = $this->errorFactory->create(5, 'error message', 'SomeClass', $fileInfo);
        $secondError = $this->errorFactory->create(15, 'error message', 'SomeClass', $fileInfo);
        $thirdError = $this->errorFactory->create(5, 'error message', 'SomeClass', $fileInfo);

        return [
            'filePath' => [$firstError],
            'anotherFilePath' => [$secondError, $thirdError],
        ];
    }
}
