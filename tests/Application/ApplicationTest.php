<?php declare(strict_types = 1);

namespace Symplify\EasyCodingStandard\Tests\Application;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Application\Application;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class ApplicationTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        $container = (new GeneralContainerFactory())->createFromConfig(
            __DIR__ . '/../../src/config/config.neon'
        );

        $this->application = $container->getByType(Application::class);
    }

    public function testFileProcessorsAreLoaded()
    {
        $this->assertCount(2, Assert::getObjectAttribute($this->application, 'fileProcessors'));
    }
}
