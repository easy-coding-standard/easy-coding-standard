<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Tests\Runner;

use Nette\Neon\Neon;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\FixerRunner\Fixer\FixerFactory;
use Symplify\EasyCodingStandard\FixerRunner\Runner\Runner;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class RunnerTest extends TestCase
{
    /**
     * @var Runner
     */
    private $runner;

    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    protected function setUp()
    {
        $container = (new GeneralContainerFactory())->createFromConfig(
            __DIR__ . '/../../../../src/config/config.neon'
        );
        $this->runner = $container->getByType(Runner::class);
        $this->fixerFactory = $container->getByType(FixerFactory::class);
    }

    public function test()
    {
        $symfonyFixersFile = file_get_contents(__DIR__ . '/../../../../config/php-cs-fixer/symfony-fixers.neon');

        $symfonyFixersNeon = Neon::decode($symfonyFixersFile);
        $fixerClasses = $symfonyFixersNeon['php-cs-fixer']['fixers'];
        $this->runner->registerFixers($fixerClasses);

        $this->assertCount(70, Assert::getObjectAttribute($this->runner, 'fixers'));
    }
}
