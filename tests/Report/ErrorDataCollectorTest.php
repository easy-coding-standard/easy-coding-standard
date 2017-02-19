<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Report;

use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\Report\ErrorDataCollector;
use Symplify\EasyCodingStandard\FixerRunner\Application\Application as FixerRunnerApplication;
use Symplify\EasyCodingStandard\SniffRunner\Application\Application as SniffRunnerApplication;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class ErrorDataCollectorTest extends TestCase
{
    /**
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    /**
     * @var SniffRunnerApplication
     */
    private $sniffRunnerApplication;

    /**
     * @var FixerRunnerApplication
     */
    private $ruleRunnerApplication;

    protected function setUp()
    {
        $container = (new GeneralContainerFactory())->createFromConfig(
            __DIR__ . '/../../src/config/config.neon'
        );
        $this->errorDataCollector = $container->getByType(ErrorDataCollector::class);
        $this->sniffRunnerApplication = $container->getByType(SniffRunnerApplication::class);
        $this->ruleRunnerApplication = $container->getByType(FixerRunnerApplication::class);
    }

    public function testEmptyState()
    {
        $this->assertSame(0, $this->errorDataCollector->getErrorCount());
        $this->assertSame([], $this->errorDataCollector->getErrorMessages());

        $this->assertSame(0, $this->errorDataCollector->getFixableErrorCount());

        $this->assertSame([], $this->errorDataCollector->getUnfixableErrorMessages());
    }

    public function testSniffRunner()
    {
        $runCommand = RunApplicationCommand::createFromSourceFixerAndData(
            [__DIR__ . '/ErrorDataCollectorSource/'],
            false,
            [
                'php-code-sniffer' => [ClassDeclarationSniff::class]
            ]
        );
        $this->sniffRunnerApplication->runCommand($runCommand);

        $this->assertSame(1, $this->errorDataCollector->getErrorCount());
        $this->assertSame(1, $this->errorDataCollector->getFixableErrorCount());

        $errorMessages = $this->errorDataCollector->getErrorMessages();
        $this->assertCount(1, $errorMessages);

        $this->assertStringEndsWith('Report/ErrorDataCollectorSource/NotPsr2Class.php', key($errorMessages));
        $this->assertSame([
            'line' => 6,
            'message' => 'Opening class brace must be on a line by itself',
            'sourceClass' => 'OpenBraceNotAlone',
            'isFixable' => true,
        ], array_pop($errorMessages)[0]);
    }

    public function testFixerRunner()
    {
        $runCommand = RunApplicationCommand::createFromSourceFixerAndData(
            [__DIR__ . '/ErrorDataCollectorSource/'],
            false,
            [
                'php-cs-fixer' => [DeclareStrictTypesFixer::class]
            ]
        );

        $this->ruleRunnerApplication->runCommand($runCommand);

        $this->assertSame(1, $this->errorDataCollector->getErrorCount());
        $this->assertSame(1, $this->errorDataCollector->getFixableErrorCount());

        $errorMessages = $this->errorDataCollector->getErrorMessages();
        $this->assertCount(1, $errorMessages);

        $this->assertStringEndsWith('Report/ErrorDataCollectorSource/NotPsr2Class.php', key($errorMessages));
        $this->assertSame([
            'line' => 0,
            'message' => 'Force strict types declaration in all files. Requires PHP >= 7.0.',
            'sourceClass' => DeclareStrictTypesFixer::class,
            'isFixable' => true,
        ], array_pop($errorMessages)[0]);
    }
}
