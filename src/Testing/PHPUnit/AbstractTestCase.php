<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Testing\PHPUnit;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\DependencyInjection\LazyContainerFactory;
use ECSPrefix202606\Webmozart\Assert\Assert;
abstract class AbstractTestCase extends TestCase
{
    /**
     * @var \Symplify\EasyCodingStandard\Config\ECSConfig|null
     */
    private $ecsConfig;
    protected function setUp(): void
    {
        $lazyContainerFactory = new LazyContainerFactory();
        $this->ecsConfig = $lazyContainerFactory->create();
        $this->ecsConfig->boot();
    }
    /**
     * @param string[] $configs
     */
    protected function createContainerWithConfigs(array $configs): void
    {
        Assert::allString($configs);
        Assert::allFile($configs);
        $lazyContainerFactory = new LazyContainerFactory();
        $this->ecsConfig = $lazyContainerFactory->create($configs);
        $this->ecsConfig->boot();
    }
    /**
     * @template TObject as object
     *
     * @param class-string<TObject> $class
     * @return TObject
     */
    protected function make(string $class): object
    {
        Assert::notNull($this->ecsConfig);
        return $this->ecsConfig->make($class);
    }
}
