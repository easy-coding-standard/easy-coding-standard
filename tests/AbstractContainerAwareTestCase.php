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

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->container = (new ContainerFactory)->create();

        parent::__construct($name, $data, $dataName);
    }
}
