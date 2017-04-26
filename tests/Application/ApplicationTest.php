<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Application;

use PHPUnit\Framework\Assert;
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
        $this->application = $this->container->getByType(Application::class);
    }

    public function testFileProcessorsAreLoaded(): void
    {
        $this->assertCount(2, Assert::getObjectAttribute($this->application, 'fileProcessors'));
    }
}
