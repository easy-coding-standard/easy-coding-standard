<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Error;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DI\ContainerFactory;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorSorter;

final class ErrorSorterTest extends TestCase
{
    /**
     * @var ErrorSorter
     */
    private $errorSorter;

    public function setUp(): void
    {
        $container = (new ContainerFactory)->create();
        $this->errorSorter = $container->getByType(ErrorSorter::class);
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
}
