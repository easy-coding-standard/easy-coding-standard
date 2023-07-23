<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Testing;

use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DependencyInjection\NewContainerFactory;
use Webmozart\Assert\Assert;

abstract class AbstractTestCase extends TestCase
{
    private ?Container $container = null;

    protected function setUp(): void
    {
        $newContainerFactory = new NewContainerFactory();
        $this->container = $newContainerFactory->create();
    }

    /**
     * @template TObject as object
     *
     * @param class-string<TObject> $class
     * @return TObject
     */
    protected function make(string $class): object
    {
        Assert::notNull($this->container);

        return $this->container->make($class);
    }
}
