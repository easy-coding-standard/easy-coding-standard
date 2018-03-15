<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Testing;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\PackageBuilder\FileSystem\FileGuard;
use Symplify\TokenRunner\Testing\AbstractSimpleFixerTestCase;

abstract class AbstractContainerAwareCheckerTestCase extends AbstractSimpleFixerTestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected function setUp(): void
    {
        FileGuard::ensureFileExists($this->provideConfig(), get_called_class());
        $this->container = (new ContainerFactory())->createWithConfig($this->provideConfig());

        parent::setUp();
    }

    abstract protected function provideConfig(): string;
}
