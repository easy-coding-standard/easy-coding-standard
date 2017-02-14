<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Report;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\DI\ContainerFactory;
use Symplify\EasyCodingStandard\Report\ErrorDataCollector;
use Symplify\EasyCodingStandard\RuleRunner\Application\Application as RuleRunnerApplication;
use Symplify\EasyCodingStandard\SniffRunner\Application\Application as SniffRunnerApplication;

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
     * @var RuleRunnerApplication
     */
    private $ruleRunnerApplication;

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $this->errorDataCollector = $container->getByType(ErrorDataCollector::class);
        $this->sniffRunnerApplication = $container->getByType(SniffRunnerApplication::class);
        $this->ruleRunnerApplication = $container->getByType(RuleRunnerApplication::class);
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
                    'standards' => ['PSR2']
                ]
            ]
        );
        $this->sniffRunnerApplication->runCommand($runCommand);

        $this->assertSame(1, $this->errorDataCollector->getErrorCount());
        $this->assertSame(1, $this->errorDataCollector->getFixableErrorCount());
        $this->assertSame(0, $this->errorDataCollector->getUnfixableErrorCount());

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
        $runCommand = RunApplicationCommand::createFromSourceFixerAndData(
            [__DIR__ . '/ErrorDataCollectorSource/'],
            false,
            [
                'php-cs-fixer' => [
                    'rules' => ['declare_strict_types']
                ]
            ]
        );

        $this->ruleRunnerApplication->runCommand($runCommand);

        $this->assertSame(1, $this->errorDataCollector->getErrorCount());
        $this->assertSame(1, $this->errorDataCollector->getFixableErrorCount());
        $this->assertSame(0, $this->errorDataCollector->getUnfixableErrorCount());

        $errorMessages = $this->errorDataCollector->getErrorMessages();
        $this->assertCount(1, $errorMessages);

        $this->assertStringEndsWith('Report/ErrorDataCollectorSource/NotPsr2Class.php', key($errorMessages));
        $this->assertSame([
            'line' => 0,
            'message' => 'Force strict types declaration in all files. Requires PHP >= 7.0.',
            'sniffClass' => DeclareStrictTypesFixer::class,
            'isFixable' => true,
        ], array_pop($errorMessages)[0]);
    }
}
