<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Testing\PHPUnit;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DependencyInjection\NewContainerFactory;
use ECSPrefix202309\Webmozart\Assert\Assert;
abstract class AbstractTestCase extends TestCase
{
    /**
     * @var \Illuminate\Container\Container|null
     */
    private $container;
    protected function setUp() : void
    {
        $newContainerFactory = new NewContainerFactory();
        $this->container = $newContainerFactory->create();
    }
    /**
     * @param string[] $configs
     */
    protected function createContainerWithConfigs(array $configs) : void
    {
        Assert::allString($configs);
        Assert::allFile($configs);
        $newContainerFactory = new NewContainerFactory();
        $this->container = $newContainerFactory->create($configs);
    }
    /**
     * @template TObject as object
     *
     * @param class-string<TObject> $class
     * @return TObject
     */
    protected function make(string $class) : object
    {
        Assert::notNull($this->container);
        return $this->container->make($class);
    }
}
