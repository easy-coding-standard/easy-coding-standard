<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner;

use SebastianBergmann\Diff\Differ;

final class ChangedLinesDetector
{
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
        foreach ($diffTokens as $key => $diffToken) {
            if ($diffToken[1] === 2) { // line was added removed
                $changedLines[] = $currentLine;

                if (! isset($diffTokens[$key + 1])) {
                    continue;
                }

                if ($diffTokens[$key + 1][1] === 1) { // next line was added
                    continue;
                }
            }

            ++$currentLine;
        }

        return $changedLines;
    }
}
