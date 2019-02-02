<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Symplify\EasyCodingStandard\Configuration\Exception\OutputFormatterNotFoundException;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;

final class OutputFormatterCollector
{
    /**
     * @var OutputFormatterInterface[]
     */
    private $outputFormatters = [];

    /**
     * @param OutputFormatterInterface[] $outputFormatters
     */
    public function __construct(array $outputFormatters)
    {
        foreach ($outputFormatters as $outputFormatter) {
            $this->outputFormatters[$outputFormatter->getName()] = $outputFormatter;
        }
    }

    public function getByName(string $name): OutputFormatterInterface
    {
        if (isset($this->outputFormatters[$name])) {
            return $this->outputFormatters[$name];
        }

        throw new OutputFormatterNotFoundException(sprintf(
            'Output formatter "%s" not found. Use one of: "%s".',
            $name,
            implode('", "', array_keys($this->outputFormatters))
        ));
    }
}
