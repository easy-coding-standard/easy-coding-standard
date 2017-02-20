<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Report;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Report\ErrorMessageSorter;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class ErrorMessageSorterTest extends TestCase
{
    public function test()
    {
        $container = (new GeneralContainerFactory)->createFromConfig(__DIR__ . '/../../../../src/config/config.neon');
        $errorMessageSorter = $container->getByType(ErrorMessageSorter::class);

        $this->assertSame(
            $this->getExpectedSortedMessages(),
            $errorMessageSorter->sortByFileAndLine($this->getUnsortedMessages())
        );
    }

    private function getUnsortedMessages() : array
    {
        return [
            'filePath' => [
                [
                    'line' => 5
                ]
            ],
            'anotherFilePath' => [
                [
                    'line' => 15
                ], [
                    'line' => 5
                ]
            ]
        ];
    }

    private function getExpectedSortedMessages() : array
    {
        return [
            'anotherFilePath' => [
                [
                    'line' => 5
                ], [
                    'line' => 15
                ]
            ],
            'filePath' => [
                [
                    'line' => 5
                ]
            ]
        ];
    }
}
