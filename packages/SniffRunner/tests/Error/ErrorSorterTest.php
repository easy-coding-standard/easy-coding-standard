<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Error;

use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorFactory;
use Symplify\EasyCodingStandard\Error\ErrorSorter;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;

final class ErrorSorterTest extends AbstractContainerAwareTestCase
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
        $this->errorSorter = $this->container->get(ErrorSorter::class);
        $this->errorFactory = $this->container->get(ErrorFactory::class);
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
            'filePath' => [$this->errorFactory->createFromLineMessageSourceClass(5, 'error message', 'SomeClass')],
            'anotherFilePath' => [
                $this->errorFactory->createFromLineMessageSourceClass(15, 'error message', 'SomeClass'),
                $this->errorFactory->createFromLineMessageSourceClass(5, 'error message', 'SomeClass'),
            ],
        ];
    }
}
