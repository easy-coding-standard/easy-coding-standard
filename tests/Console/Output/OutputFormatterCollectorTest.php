<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Output;

use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Console\Output\OutputFormatterCollector;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;

final class OutputFormatterCollectorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var OutputFormatterInterface
     */
    private $outputFormatter;

    protected function setUp(): void
    {
        $this->outputFormatter = $this->container->get(JsonOutputFormatter::class);
    }

    public function testCanGetFormatterByName(): void
    {
        $formatters = [$this->outputFormatter];
        $collector = new OutputFormatterCollector($formatters);
        $name = $this->outputFormatter->getName();

        $this->assertSame($this->outputFormatter, $collector->getByName($name));
    }

    /**
     * @expectedException \Symplify\EasyCodingStandard\Configuration\Exception\OutputFormatterNotFoundException
     */
    public function testThrowOnWrongName(): void
    {
        $formatters = [$this->outputFormatter];
        $collector = new OutputFormatterCollector($formatters);
        $name = 'wrong';

        $collector->getByName($name);
    }
}
