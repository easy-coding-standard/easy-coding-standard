<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Report;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\SniffRunner\DI\ContainerFactory;
use Symplify\EasyCodingStandard\SniffRunner\Report\ErrorDataCollector;

final class ErrorDataCollectorTest extends TestCase
{
    /**
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $this->errorDataCollector = $container->getByType(ErrorDataCollector::class);

        $this->errorDataCollector->addErrorMessage('filePath', 'Message', 5, 'Code', [], false);
    }

    public function testGetCounts()
    {
        $this->assertSame(1, $this->errorDataCollector->getErrorCount());
        $this->assertSame(0, $this->errorDataCollector->getFixableErrorCount());
        $this->assertSame(1, $this->errorDataCollector->getUnfixableErrorCount());
    }

    public function testGetErrorMessages()
    {
        $messages = $this->errorDataCollector->getErrorMessages();

        $this->assertSame([
            'filePath' => [
                [
                    'line' => 5,
                    'message' => 'Message',
                    'sniffClass' => 'Code',
                    'isFixable' => false
                ]
            ]
        ], $messages);
    }

    public function testGetUnfixableErrorMessage()
    {
        $this->assertSame(
            $this->errorDataCollector->getErrorMessages(),
            $this->errorDataCollector->getUnfixableErrorMessages()
        );

        $this->errorDataCollector->addErrorMessage('filePath', 'Message 2', 3, 'Code', [], true);

        $this->assertNotSame(
            $this->errorDataCollector->getErrorMessages(),
            $this->errorDataCollector->getUnfixableErrorMessages()
        );
    }
}
