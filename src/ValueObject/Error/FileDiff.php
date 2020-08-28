<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ValueObject\Error;

final class FileDiff
{
    /**
     * @var string
     */
    private $diff;

    /**
     * @var string[]
     */
    private $appliedCheckers = [];

    /**
     * @var string
     */
    private $consoleFormattedDiff;

    /**
     * @param string[] $appliedCheckers
     */
    public function __construct(string $diff, string $consoleFormattedDiff, array $appliedCheckers)
    {
        $this->diff = $diff;
        $this->appliedCheckers = $appliedCheckers;
        $this->consoleFormattedDiff = $consoleFormattedDiff;
    }

    public function getDiff(): string
    {
        return $this->diff;
    }

    public function getDiffConsoleFormatted(): string
    {
        return $this->consoleFormattedDiff;
    }

    /**
     * @return string[]
     */
    public function getAppliedCheckers(): array
    {
        $this->appliedCheckers = array_unique($this->appliedCheckers);
        sort($this->appliedCheckers);

        return $this->appliedCheckers;
    }
}
