<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;

abstract class AbstractConfigContainerAwareTestCase extends TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param mixed[] $data
     * @param int|string $dataName
     */
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->container = (new ContainerFactory())->createWithConfigs([$this->provideConfig()]);

        parent::__construct($name, $data, $dataName);
    }

    abstract protected function provideConfig(): string;
}
