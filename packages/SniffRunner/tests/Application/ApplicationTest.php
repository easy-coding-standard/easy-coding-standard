<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Application\Command\RunApplicationCommand;
use Symplify\EasyCodingStandard\SniffRunner\Application\Application;
use Symplify\PackageBuilder\Adapter\Nette\ContainerFactory;

final class ApplicationTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        $container = (new ContainerFactory())->createFromConfig(__DIR__ . '/../../src/config/config.neon');
        $this->application = $container->getByType(Application::class);
    }

    public function testRunCommand()
    {
        $this->application->runCommand($this->createCommand());
        $this->assertTrue(true);
    }

    private function createCommand() : RunApplicationCommand
    {
        return RunApplicationCommand::createFromSourceFixerAndData(
            $source = [__DIR__ . '/ApplicationSource'],
            $isFixer = true,
            [
                'sniffs' => '',
            ]
        );
    }
}
