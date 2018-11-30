<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Symplify\EasyCodingStandard\Configuration\Exception\NoOutputFormatterException;
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
        if (! isset($this->outputFormatters[$name])) {
            throw new NoOutputFormatterException(
                sprintf(
                    'Output formatter not found. Currently available: %s.',
                    implode(', ', array_keys($this->outputFormatters))
                )
            );
        }

        return $this->outputFormatters[$name];
    }
}
