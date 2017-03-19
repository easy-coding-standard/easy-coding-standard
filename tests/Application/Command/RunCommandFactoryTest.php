<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Application\Command;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Sniffs\Classes\ClassDeclarationSniff;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\Application\Command\RunCommandFactory;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\ConfigurationOptions;

final class RunCommandFactoryTest extends TestCase
{
    /**
     * @var RunCommandFactory
     */
    private $runCommandFactory;

    protected function setUp(): void
    {
        $this->runCommandFactory = new RunCommandFactory(new ConfigurationNormalizer);
    }

    public function testEmpty(): void
    {
        $runCommand = $this->createWithConfiguration([]);

        $this->assertSame([__DIR__], $runCommand->getSources());
        $this->assertFalse($runCommand->isFixer());
        $this->assertFalse($runCommand->shouldClearCache());
        $this->assertEmpty($runCommand->getSkipped());
        $this->assertEmpty($runCommand->getSniffs());
        $this->assertEmpty($runCommand->getFixers());
    }

    public function testSniffsAndFixers(): void
    {
        $runCommand = $this->createWithConfiguration([
            ConfigurationOptions::CHECKERS => [
                ClassDeclarationSniff::class,
                DeclareStrictTypesFixer::class
            ]
        ]);

        $sniffs = $runCommand->getSniffs();
        $this->assertCount(1, $sniffs);
        $this->assertSame([ClassDeclarationSniff::class => []], $sniffs);

        $fixers = $runCommand->getFixers();
        $this->assertCount(1, $fixers);
        $this->assertSame([DeclareStrictTypesFixer::class => []], $fixers);
    }

    public function testSkipped(): void
    {
        $runCommand = $this->createWithConfiguration([
           ConfigurationOptions::SKIP => [
                DeclareStrictTypesFixer::class => [
                    'someFile.php'
                ]
           ]
        ]);

        $this->assertSame([DeclareStrictTypesFixer::class => [
            'someFile.php'
        ]], $runCommand->getSkipped());
    }

    /**
     * @param mixed[][] $configuration
     */
    private function createWithConfiguration(array $configuration): RunCommand
    {
        return $this->runCommandFactory->create([__DIR__], false, false, $configuration);
    }
}
