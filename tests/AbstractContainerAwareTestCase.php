<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests;

use Nette\DI\Container;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DI\ContainerFactory;

abstract class AbstractContainerAwareTestCase extends TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param mixed[] $data
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        $this->container = (new ContainerFactory)->create();

        parent::__construct($name, $data, $dataName);
    }
}
