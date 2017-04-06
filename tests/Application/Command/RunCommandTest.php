<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Application\Command;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Sniffs\Classes\ClassDeclarationSniff;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\Application\Command\RunCommandFactory;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\ConfigurationOptions;

final class RunCommandTest extends TestCase
{
    public function testConfiguration(): void
    {
        $runCommand = $this->createRunCommandWithConfiguration([]);
        $this->assertEmpty($runCommand->getSkipped());
        $this->assertEmpty($runCommand->getConfiguration());
        $this->assertEmpty($runCommand->getSniffs());
        $this->assertSame([__DIR__], $runCommand->getSources());
        $this->assertFalse($runCommand->isFixer());
        $this->assertFalse($runCommand->shouldClearCache());
    }

    public function testSniffs(): void
    {
        $runCommand = $this->createRunCommandWithConfiguration([
            ConfigurationOptions::CHECKERS => [
                ClassDeclarationSniff::class,
                DeclareStrictTypesFixer::class
            ]
        ]);

        $this->assertCount(1, $runCommand->getSniffs());
        $this->assertSame([ClassDeclarationSniff::class => []], $runCommand->getSniffs());
    }

    public function testFixers(): void
    {
        $runCommand = $this->createRunCommandWithConfiguration([
            ConfigurationOptions::CHECKERS => [
                ClassDeclarationSniff::class,
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
        $runCommandFactory = new RunCommandFactory(new ConfigurationNormalizer);

        return $runCommandFactory->create(
            [__DIR__],
            false,
            false,
            $configuration
        );
    }
}
