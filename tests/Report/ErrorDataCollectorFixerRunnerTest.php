<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Report;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\FixerRunner\Application\Application;
use Symplify\EasyCodingStandard\Report\Error\Error;
use Symplify\EasyCodingStandard\Report\ErrorDataCollector;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class ErrorDataCollectorFixerRunnerTest extends TestCase
{
    /**
     * @var ErrorDataCollector
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
        $this->errorDataCollector = $container->getByType(ErrorDataCollector::class);
        $this->application = $container->getByType(Application::class);
    }

    public function testFixerRunner()
    {
        $runCommand = RunApplicationCommand::createFromSourceFixerAndData(
            [__DIR__ . '/ErrorDataCollectorSource/NotPsr2Class.php.inc'],
            false,
            [
                'php-cs-fixer' => [DeclareStrictTypesFixer::class]
            ]
        );

        $this->application->runCommand($runCommand);

        $this->assertSame(1, $this->errorDataCollector->getErrorCount());
        $this->assertSame(1, $this->errorDataCollector->getFixableErrorCount());

        $errorMessages = $this->errorDataCollector->getErrorMessages();
        $this->assertCount(1, $errorMessages);

        $this->assertStringEndsWith('Report/ErrorDataCollectorSource/NotPsr2Class.php.inc', key($errorMessages));
        $this->assertEquals(new Error(
            0,
            'Force strict types declaration in all files. Requires PHP >= 7.0.',
            DeclareStrictTypesFixer::class,
            true
        ), array_pop($errorMessages)[0]);
    }
}
