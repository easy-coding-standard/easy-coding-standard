<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use PhpCsFixer\Differ\DiffConsoleFormatter;

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
     * @var DiffConsoleFormatter
     */
    private $diffConsoleFormatter;

    /**
     * @param string[] $appliedCheckers
     */
    public function __construct(string $diff, array $appliedCheckers)
    {
        $this->diff = $diff;
        $this->appliedCheckers = $appliedCheckers;

        $this->diffConsoleFormatter = new DiffConsoleFormatter(true, sprintf(
            '<comment>    ---------- begin diff ----------</comment>' .
            '%s%%s%s' .
            '<comment>    ----------- end diff -----------</comment>',
            PHP_EOL,
            PHP_EOL
        ));
    }

    public function getDiff(): string
    {
        return $this->diff;
    }

    public function getDiffConsoleFormatted(): string
    {
        return $this->diffConsoleFormatter->format($this->diff);
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
