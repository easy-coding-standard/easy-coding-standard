<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

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
     * @param string[] $appliedCheckers
     */
    public function __construct(string $diff, array $appliedCheckers)
    {
        $this->diff = $diff;
        $this->appliedCheckers = $appliedCheckers;
    }

    public function getDiff(): string
    {
        return $this->diff;
    }

    /**
     * @return string[]
     */
    public function getAppliedCheckers(): array
    {
        return $this->appliedCheckers;
    }
}
