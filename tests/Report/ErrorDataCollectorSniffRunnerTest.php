<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Report;

use PHP_CodeSniffer\Standards\PEAR\Sniffs\Classes\ClassDeclarationSniff as PearClassDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\Report\Error\Error;
use Symplify\EasyCodingStandard\Report\ErrorCollector;
use Symplify\EasyCodingStandard\SniffRunner\Application\Application;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class ErrorDataCollectorSniffRunnerTest extends TestCase
{
    /**
     * @var ErrorCollector
     */
    private $errorDataCollector;

    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        $container = (new GeneralContainerFactory)->createFromConfig(
            __DIR__ . '/../../src/config/config.neon'
        );
        $this->errorDataCollector = $container->getByType(ErrorCollector::class);
        $this->application = $container->getByType(Application::class);
    }

    public function testSniffRunner()
    {
        $runCommand = RunApplicationCommand::createFromSourceFixerAndData(
            [__DIR__ . '/ErrorDataCollectorSource/NotPsr2Class.php.inc'],
            false,
            [
                'php-code-sniffer' => [ClassDeclarationSniff::class]
            ]
        );
        $this->application->runCommand($runCommand);

        $this->assertSame(1, $this->errorDataCollector->getErrorCount());
        $this->assertSame(1, $this->errorDataCollector->getFixableErrorCount());

        $errorMessages = $this->errorDataCollector->getErrors();
        $this->assertCount(1, $errorMessages);

        $this->assertStringEndsWith('Report/ErrorDataCollectorSource/NotPsr2Class.php.inc', key($errorMessages));

        /** @var Error $error */
        $error = array_pop($errorMessages)[0];
        $this->assertInstanceOf(Error::class, $error);

        $this->assertSame(6, $error->getLine());
        $this->assertSame('Opening class brace must be on a line by itself', $error->getMessage());
        $this->assertSame(PearClassDeclarationSniff::class, $error->getSourceClass());
        $this->assertSame(true, $error->isFixable());
    }
}
