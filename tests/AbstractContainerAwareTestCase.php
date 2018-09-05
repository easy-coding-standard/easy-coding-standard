<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;

abstract class AbstractContainerAwareTestCase extends TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ContainerInterface
     */
    private static $cachedContainer;

    /**
     * @param mixed[] $data
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        if (self::$cachedContainer === null) {
            self::$cachedContainer = (new ContainerFactory())->create();
        }

        $this->container = self::$cachedContainer;

        parent::__construct($name, $data, $dataName);
    }
}
