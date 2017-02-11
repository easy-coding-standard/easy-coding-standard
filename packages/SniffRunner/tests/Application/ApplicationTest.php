<?php declare(strict_types=1);

namespace Symplify\SniffRunner\Tests\Application;

use PHPUnit\Framework\TestCase;
use Symplify\SniffRunner\Application\Application;
use Symplify\SniffRunner\Application\Command\RunApplicationCommand;
use Symplify\SniffRunner\DI\ContainerFactory;
use Symplify\SniffRunner\Tests\Instantiator;

final class ApplicationTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $this->application = $container->getByType(Application::class);
    }

    public function testRunCommand()
    {
        $this->application->runCommand($this->createCommand());
    }

    private function createCommand() : RunApplicationCommand
    {
        return new RunApplicationCommand(
            $source = [__DIR__ . '/ApplicationSource'],
            $standards = ['PSR2'],
            $sniffs = [],
            $excludedSniffs = [],
            $isFixer = true
        );
    }
}
