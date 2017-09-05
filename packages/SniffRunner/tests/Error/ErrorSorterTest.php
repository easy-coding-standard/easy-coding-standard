<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Error;

use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorSorter;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;

final class ErrorSorterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ErrorSorter
     */
    private $errorSorter;

    public function setUp(): void
    {
        $this->errorSorter = $this->container->get(ErrorSorter::class);
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
                Error::createFromLineMessageSourceClassAndFixable(5, 'error message', 'SomeClass', false),
            ],
            'anotherFilePath' => [
                Error::createFromLineMessageSourceClassAndFixable(15, 'error message', 'SomeClass', false),
                Error::createFromLineMessageSourceClassAndFixable(5, 'error message', 'SomeClass', false),
            ],
        ];
    }
}
