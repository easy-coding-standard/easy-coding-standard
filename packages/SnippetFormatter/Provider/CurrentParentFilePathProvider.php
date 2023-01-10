<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Provider;

final class CurrentParentFilePathProvider
{
    private ?string $filePath = null;

    public function setParentFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    public function provide(): ?string
    {
        return $this->filePath;
    }
}
