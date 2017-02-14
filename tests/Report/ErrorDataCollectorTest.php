<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Report;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\DI\ContainerFactory;
use Symplify\EasyCodingStandard\Report\ErrorDataCollector;
use Symplify\EasyCodingStandard\SniffRunner\Application\Application;

final class ErrorDataCollectorTest extends TestCase
{
    /**
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    /**
     * @var Application
     */
    private $sniffRunnerApplication;

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $this->errorDataCollector = $container->getByType(ErrorDataCollector::class);
        $this->sniffRunnerApplication = $container->getByType(Application::class);
    }

    public function testEmptyState()
    {
        $this->assertSame(0, $this->errorDataCollector->getErrorCount());
        $this->assertSame([], $this->errorDataCollector->getErrorMessages());

        $this->assertSame(0, $this->errorDataCollector->getFixableErrorCount());

        $this->assertSame(0, $this->errorDataCollector->getUnfixableErrorCount());
        $this->assertSame([], $this->errorDataCollector->getUnfixableErrorMessages());
    }

    public function testSniffRunner()
    {
        $runCommand = RunApplicationCommand::createFromSourceFixerAndData(
            [__DIR__ . '/ErrorDataCollectorSource/'],
            false,
            [
                'php-code-sniffer' => [
                    'standards' => [
                        'PSR2'
                    ]
                ]
            ]
        );
        $this->sniffRunnerApplication->runCommand($runCommand);

        $this->assertSame(1, $this->errorDataCollector->getErrorCount());

        $errorMessages = $this->errorDataCollector->getErrorMessages();
        $this->assertCount(1, $errorMessages);

        $this->assertStringEndsWith('Report/ErrorDataCollectorSource/NotPsr2Class.php', key($errorMessages));
        $this->assertSame([
            'line' => 6,
            'message' => 'Opening class brace must be on a line by itself',
            'sniffClass' => 'OpenBraceNotAlone',
            'isFixable' => true,
        ], array_pop($errorMessages)[0]);
    }

    public function testRuleRunner()
    {

    }
}
