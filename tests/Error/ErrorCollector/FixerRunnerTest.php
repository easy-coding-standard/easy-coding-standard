<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\FixerRunner\Application\Application;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class FixerRunnerTest extends TestCase
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
        $this->runApplicationWithFixer(PhpUnitStrictFixer::class);

        $this->assertSame(1, $this->errorDataCollector->getErrorCount());
        $this->assertSame(1, $this->errorDataCollector->getFixableErrorCount());
        $this->assertSame(0, $this->errorDataCollector->getUnfixableErrorCount());

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
            [__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc'], false, [
                'php-cs-fixer' => [$fixerClass]
            ]
        );

        $this->application->runCommand($runCommand);
    }
}
