<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Report;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Report\Error\Error;
use Symplify\EasyCodingStandard\Report\ErrorSorter;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class ErrorMessageSorterTest extends TestCase
{
    public function test()
    {
        $container = (new GeneralContainerFactory)->createFromConfig(__DIR__ . '/../../../../src/config/config.neon');
        $errorMessageSorter = $container->getByType(ErrorSorter::class);

        $this->assertEquals(
            $this->getExpectedSortedMessages(),
            $errorMessageSorter->sortByFileAndLine($this->getUnsortedMessages())
        );
    }

    private function getUnsortedMessages(): array
    {
        return [
            'filePath' => [
                new Error(5, 'error', 'SomeClass', false)
            ],
            'anotherFilePath' => [
                new Error(15, 'error', 'SomeClass', false),
                new Error(5, 'error', 'SomeClass', false)
            ]
        ];
    }

    private function getExpectedSortedMessages(): array
    {
        return [
            'anotherFilePath' => [
                new Error(5, 'error', 'SomeClass', false),
                new Error(15, 'error', 'SomeClass', false)
            ],
            'filePath' => [
                new Error(5, 'error', 'SomeClass', false)
            ]
        ];
    }
}
