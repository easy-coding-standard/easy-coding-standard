<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Application;

use Symplify\EasyCodingStandard\Application\Application;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class ApplicationTest extends AbstractKernelTestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $this->application = self::$container->get(Application::class);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testRun(): void
    {
        $this->application->run();
    }
}
