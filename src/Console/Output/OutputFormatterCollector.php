<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Output;

use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\Exception\Configuration\OutputFormatterNotFoundException;
final class OutputFormatterCollector
{
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;
    /**
     * Formats dropped as ECS is a fixer, not a static analyzer; each maps to a still-supported fallback.
     *
     * @var array<string, string>
     */
    private const REMOVED_FORMATS = ['junit' => \Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter::NAME, 'gitlab' => \Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter::NAME];
    /**
     * @var array<string, OutputFormatterInterface>
     */
    private $outputFormatters = [];
    /**
     * @param OutputFormatterInterface[] $outputFormatters
     */
    public function __construct(array $outputFormatters, EasyCodingStandardStyle $easyCodingStandardStyle)
    {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        foreach ($outputFormatters as $outputFormatter) {
            $this->outputFormatters[$outputFormatter->getName()] = $outputFormatter;
        }
    }
    public function getByName(string $name): OutputFormatterInterface
    {
        if (isset($this->outputFormatters[$name])) {
            return $this->outputFormatters[$name];
        }
        if (isset(self::REMOVED_FORMATS[$name])) {
            $fallback = self::REMOVED_FORMATS[$name];
            $this->easyCodingStandardStyle->warning(sprintf('The "%s" output format was removed, as ECS is a fixer, not a static analyzer. Falling back to "%s".', $name, $fallback));
            return $this->outputFormatters[$fallback];
        }
        $outputFormatterKeys = array_keys($this->outputFormatters);
        $errorMessage = sprintf('Output formatter "%s" not found. Use one of: "%s".', $name, implode('", "', $outputFormatterKeys));
        throw new OutputFormatterNotFoundException($errorMessage);
    }
}
