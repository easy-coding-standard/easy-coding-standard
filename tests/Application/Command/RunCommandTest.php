<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Application\Command;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Sniffs\Classes\FinalInterfaceSniff;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\Application\Command\RunCommandFactory;
use Symplify\EasyCodingStandard\Configuration\ConfigurationOptions;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class RunCommandTest extends TestCase
{
    /**
     * @var RunCommandFactory
     */
    private $runCommandFactory;

    protected function setUp(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(
            __DIR__ . '/../../../src/config/config.neon'
        );

        $this->runCommandFactory = $container->getByType(RunCommandFactory::class);
    }

    public function testConfiguration(): void
    {
        $runCommand = $this->createRunCommandWithConfiguration([]);
        $this->assertSame([
            ConfigurationOptions::CHECKERS => [],
            ConfigurationOptions::SKIP => []
        ], $runCommand->getConfiguration());
        $this->assertEmpty($runCommand->getSniffs());
        $this->assertSame([__DIR__], $runCommand->getSources());
        $this->assertFalse($runCommand->isFixer());
        $this->assertFalse($runCommand->shouldClearCache());
    }

    public function testSniffs(): void
    {
        $runCommand = $this->createRunCommandWithConfiguration([
            ConfigurationOptions::CHECKERS => [
                FinalInterfaceSniff::class,
                DeclareStrictTypesFixer::class
            ]
        ]);

        $this->assertCount(1, $runCommand->getSniffs());
        $this->assertSame([FinalInterfaceSniff::class => []], $runCommand->getSniffs());
    }

    public function testFixers(): void
    {
        $runCommand = $this->createRunCommandWithConfiguration([
            ConfigurationOptions::CHECKERS => [
                FinalInterfaceSniff::class,
                DeclareStrictTypesFixer::class
            ]
        ]);

        $this->assertCount(1, $runCommand->getFixers());
        $this->assertSame([DeclareStrictTypesFixer::class => []], $runCommand->getFixers());
    }

    /**
     * @param mixed[] $configuration
     */
    private function createRunCommandWithConfiguration(array $configuration = []): RunCommand
    {
        return $this->runCommandFactory->create(
            [__DIR__],
            false,
            false,
            $configuration
        );
    }
}
