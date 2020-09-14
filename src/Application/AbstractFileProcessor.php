<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\Console\Command\CheckMarkdownCommand;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Provider\CurrentParentFileInfoProvider;
use Symplify\SmartFileSystem\SmartFileInfo;

abstract class AbstractFileProcessor implements FileProcessorInterface
{
    /**
     * @var CurrentParentFileInfoProvider
     */
    protected $currentParentFileInfoProvider;

    /**
     * @required
     */
    public function autowireAbstractFileProcessor(CurrentParentFileInfoProvider $currentParentFileInfoProvider): void
    {
        $this->currentParentFileInfoProvider = $currentParentFileInfoProvider;
    }

    /**
     * Useful for @see CheckMarkdownCommand
     * Where the $smartFileInfo is only temporary snippet, so original markdown file should be used
     */
    protected function resolveTargetFileInfo(SmartFileInfo $smartFileInfo): SmartFileInfo
    {
        $currentParentFileInfo = $this->currentParentFileInfoProvider->provide();
        if ($currentParentFileInfo !== null) {
            return $currentParentFileInfo;
        }

        return $smartFileInfo;
    }
}
