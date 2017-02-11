<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Runner;

use PhpCsFixer\Runner\Runner;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DI\ContainerFactory;

final class RunnerFactoryTest extends TestCase
{
    /**
     * @var RunnerFactory
     */
    private $runnerFactory;

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $this->runnerFactory = $container->getByType(RunnerFactory::class);
    }

    public function test()
    {
        $runner = $this->runnerFactory->create(['@Symfony'], [], __DIR__, false);
        $this->assertInstanceOf(Runner::class, $runner);
        $this->assertCount(90, Assert::getObjectAttribute($runner, 'fixers'));
    }

    public function testProperties()
    {
        // @todo: test properites!
//        array_syntax
//PHP arrays should be declared using the configured syntax (requires PHP
//    >= 5.4 for short syntax).
//Rule is: configurable.


    }
}
