<?php

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
    /**
     * @param string $name
     */
    public function getByName($name) : \Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        if (isset($this->outputFormatters[$name])) {
            return $this->outputFormatters[$name];
        }
        $outputFormatterKeys = \array_keys($this->outputFormatters);
        $errorMessage = \sprintf('Output formatter "%s" not found. Use one of: "%s".', $name, \implode('", "', $outputFormatterKeys));
        throw new \Symplify\EasyCodingStandard\Configuration\Exception\OutputFormatterNotFoundException($errorMessage);
    }
}
