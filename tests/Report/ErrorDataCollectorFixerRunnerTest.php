<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Report;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\FixerRunner\Application\Application;
use Symplify\EasyCodingStandard\Report\Error\Error;
use Symplify\EasyCodingStandard\Report\ErrorCollector;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class ErrorDataCollectorFixerRunnerTest extends TestCase
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

//    public function testFixerRunner(): void
//    {
//        $this->runApplicationWithFixer(DeclareStrictTypesFixer::class);
//
//        $this->assertSame(1, $this->errorDataCollector->getErrorCount());
//        $this->assertSame(1, $this->errorDataCollector->getFixableErrorCount());
//
//        $errorMessages = $this->errorDataCollector->getErrors();
//        $this->assertCount(1, $errorMessages);
//
//        $this->assertStringEndsWith('Report/ErrorDataCollectorSource/NotPsr2Class.php.inc', key($errorMessages));
//
//        /** @var Error $error */
//        $error = array_pop($errorMessages)[0];
//        $this->assertInstanceOf(Error::class, $error);
//
//        $this->assertEquals(0, $error->getLine());
//        $this->assertEquals('Force strict types declaration in all files. Requires PHP >= 7.0.', $error->getMessage());
//        $this->assertEquals(DeclareStrictTypesFixer::class, $error->getSourceClass());
//        $this->assertEquals(true, $error->isFixable());
//    }

    public function testCorrectLine(): void
    {
        $this->runApplicationWithFixer(PhpUnitStrictFixer::class);

        $errorMessages = $this->errorDataCollector->getErrors();
        $this->assertCount(1, $errorMessages);

        /** @var Error $error */
        $error = array_pop($errorMessages)[0];
        $this->assertInstanceOf(Error::class, $error);

        $this->assertSame(
            'PHPUnit methods like `assertSame` should be used instead of `assertEquals`.',
            $error->getMessage()
        );
        $this->assertSame(9, $error->getLine());
    }

    private function runApplicationWithFixer(string $fixerClass): void
    {
        $runCommand = RunApplicationCommand::createFromSourceFixerAndData(
            [__DIR__ . '/ErrorDataCollectorSource/NotPsr2Class.php.inc'], false, [
                'php-cs-fixer' => [$fixerClass]
            ]
        );

        $this->application->runCommand($runCommand);
    }
}
