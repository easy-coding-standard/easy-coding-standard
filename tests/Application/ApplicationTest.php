<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Application;

use Symplify\EasyCodingStandard\Application\Application;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;

final class ApplicationTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp(): void
    {
        $this->application = $this->container->get(Application::class);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testRun(): void
    {
        $this->application->run();
    }
}
