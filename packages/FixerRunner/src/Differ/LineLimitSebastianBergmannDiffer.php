<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Differ;

use Nette\Utils\Strings;
use PhpCsFixer\Differ\DifferInterface;
use SebastianBergmann\Diff\Differ;

final class LineLimitSebastianBergmannDiffer implements DifferInterface
{
    /**
     * @var int
     */
    private const ADDED_LINE = 1;

    /**
     * @var int
     */
    private const REMOVED_LINE = 2;

    /**
     * @var Differ
     */
    private $differ;

    /**
     * @var int[]
     */
    private $changedLines = [];

    /**
     * @var int
     */
    private $wrapperLineCount;


    public function __construct()
    {
        $this->differ = new Differ();
        $this->wrapperLineCount = 2;
    }

    /**
     * {@inheritdoc}
     */
    public function diff($old, $new): string
    {
        $arrayDiff = $this->differ->diffToArray($old, $new);
        $arrayDiff = $this->addLineNumbers($arrayDiff);

        $result = '';
        foreach ($arrayDiff as $index => $diffItem) {
            if ($diffItem[1] === self::ADDED_LINE) {
                $result .= '+' . $diffItem[0];
            } elseif ($diffItem[1] === self::REMOVED_LINE) {
                $result .= '-' . $diffItem[0];
            } elseif ($this->getDistanceToNearestNeighbour($diffItem['line'], $this->changedLines) <= $this->wrapperLineCount) {
                $result .= $diffItem[0];
            }
        }

        return $result;
    }

    /**
     * @param mixed[] $arrayTokens
     * @return int[]
     */
    private function getChangedIndexes(array $diffItems): array
    {
        $changedIndexes = [];
        foreach ($diffItems as $index => $diffItem) {
            if ($diffItem[1] !== 0) {
                $changedIndexes[] = $index;
            }
        }

        return $changedIndexes;
    }

    /**
     * @param mixed[] $arrayDiff
     * @return mixed[]
     */
    private function addLineNumbers(array $arrayDiff): array
    {
        $line = 0;
        foreach ($arrayDiff as $i => $item) {
            if ($item[1] === self::REMOVED_LINE) { // removed
                --$line;
                $this->changedLines[] = $line;
            }

            if ($item[1] === self::ADDED_LINE) { // added
                ++$line;
                $this->changedLines[] = $line;
            }

            if (Strings::contains($item[0], PHP_EOL)) {
                ++$line;
            }

            $arrayDiff[$i]['line'] = $line;
        }

        return $arrayDiff;
    }

    /**
     * @param int[] $changedLines
     */
    private function getDistanceToNearestNeighbour(int $search, array $changedLines): int
    {
        $closest = null;

        foreach ($changedLines as $changedLine) {
            if ($closest === null || abs($search - $closest) > abs($changedLine - $search)) {
                $closest = $changedLine;
            }
        }

        return (int) abs($search - $closest);
    }
}
