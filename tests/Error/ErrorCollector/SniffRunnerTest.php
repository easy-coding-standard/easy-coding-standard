<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\SniffRunner\Application\Application;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class SniffRunnerTest extends TestCase
{
    /**
     * @var ErrorCollector
     */
    private $errorDataCollector;

    /**
     * @var Application
     */
    private $application;

    protected function setUp(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(
            __DIR__ . '/../../../src/config/config.neon'
        );
        $this->errorDataCollector = $container->getByType(ErrorCollector::class);
        $this->application = $container->getByType(Application::class);
    }

    public function test(): void
    {
        $runCommand = RunApplicationCommand::createFromSourceFixerAndData(
            [__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc'],
            false,
            [
                'php-code-sniffer' => [AbstractClassNameSniff::class]
            ]
        );
        $this->application->runCommand($runCommand);

        $this->assertSame(1, $this->errorDataCollector->getErrorCount());
        $this->assertSame(1, $this->errorDataCollector->getFixableErrorCount());
        $this->assertSame(0, $this->errorDataCollector->getUnfixableErrorCount());

        $errorMessages = $this->errorDataCollector->getErrors();
        $this->assertCount(1, $errorMessages);

        $this->assertStringEndsWith('NotPsr2Class.php.inc', key($errorMessages));

        /** @var Error $error */
        $error = array_pop($errorMessages)[0];
        $this->assertInstanceOf(Error::class, $error);

        $this->assertSame(5, $error->getLine());
        $this->assertSame('Abstract class should have prefix "Abstract".', $error->getMessage());
        $this->assertSame(AbstractClassNameSniff::class, $error->getSourceClass());
        $this->assertTrue($error->isFixable());
    }
}
