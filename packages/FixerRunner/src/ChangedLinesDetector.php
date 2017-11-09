<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner;

use SebastianBergmann\Diff\Differ;

final class ChangedLinesDetector
{
    /**
     * @var string
     */
    private const TYPE_LINE_REMOVED = 2;

    /**
     * @var string
     */
    private const TYPE_LINE_ADDED = 1;

    /**
     * @var Differ
     */
    private $differ;

    public function __construct(Differ $differ)
    {
        $this->differ = $differ;
    }

    /**
     * @return int[]
     */
    public function detectInBeforeAfter(string $oldContent, string $newContent): array
    {
        $changedLines = [];
        $currentLine = 1;

        $diffTokens = $this->differ->diffToArray($oldContent, $newContent);

        $hasRemovedLines = $this->hasRemovedLines($diffTokens);

        for ($i = 0; $i < count($diffTokens); ++$i) {
            $diffToken = $diffTokens[$i];

            if ($diffToken[1] === self::TYPE_LINE_REMOVED) { // line was removed
                $changedLines[] = $currentLine;
                if (! isset($diffTokens[$i + 1])) {
                    continue;
                }

                if ($diffTokens[$i + 1][1] === self::TYPE_LINE_ADDED) { // next line was added
                    ++$i; // do not record it twice, skip next $diffToken
                    ++$currentLine;

                    continue;
                }
            } elseif ($diffToken[1] === self::TYPE_LINE_ADDED) { // line was added
                if (! $hasRemovedLines) { // add only if that's all what has changed in file
                    $changedLines[] = $currentLine;
                }
            }

            // what if line was moved?

            ++$currentLine;
        }

        return $changedLines;
    }

    /**
     * @param mixed[] $diffTokens
     * @return bool
     */
    private function hasRemovedLines(array $diffTokens): bool
    {
        foreach ($diffTokens as $diffToken) {
            if ($diffToken[1] === self::TYPE_LINE_REMOVED) {
                return true;
            }
        }

        return false;
    }
}
