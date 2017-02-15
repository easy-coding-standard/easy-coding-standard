<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Runner;

use Nette\Neon\Neon;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class RunnerFactoryTest extends TestCase
{
    /**
     * @var RunnerFactory
     */
    private $runnerFactory;

    protected function setUp()
    {
        $container = (new GeneralContainerFactory())->createFromConfig(__DIR__ . '/../../../../src/config/config.neon');
        $this->runnerFactory = $container->getByType(RunnerFactory::class);
    }

    public function test()
    {
        $symfonyFixersFile = file_get_contents(__DIR__ . '/../../../../config/php-cs-fixer/symfony-fixers.neon');

        $symfonyFixersNeon = Neon::decode($symfonyFixersFile);
        $fixerClasses = $symfonyFixersNeon['php-cs-fixer']['fixers'];

        $runner = $this->runnerFactory->create($fixerClasses, __DIR__, false);
        $this->assertInstanceOf(Runner::class, $runner);
        $this->assertCount(68, Assert::getObjectAttribute($runner, 'fixers'));
    }
}
