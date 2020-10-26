<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Git;

final class GitDiffProvider
{
    /**
     * @return string[] The absolute path to the file matching the git diff shell command.
     */
    public function provide(): array
    {
        $plainDiff = shell_exec('git diff --name-only') ?: '';
        $relativePaths = explode(PHP_EOL, trim($plainDiff));

        return array_values(array_filter(array_map('realpath', $relativePaths)));
    }
}
