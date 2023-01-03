<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use Nette\Utils\FileSystem;
use PHP_CodeSniffer\Fixer;
use SplFileInfo;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FileSystem\StaticRelativeFilePathHelper;
use Symplify\EasyCodingStandard\Skipper\Skipper\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\DataCollector\SniffMetadataCollector;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;

/**
 * @see \Symplify\EasyCodingStandard\Tests\SniffRunner\File\FileFactoryTest
 */
final class FileFactory
{
    public function __construct(
        private readonly Fixer $fixer,
        private readonly Skipper $skipper,
        private readonly SniffMetadataCollector $sniffMetadataCollector,
        private readonly EasyCodingStandardStyle $easyCodingStandardStyle
    ) {
    }

    public function createFromFile(string $filePath): File
    {
        $fileContents = FileSystem::read($filePath);
        $relativeFilePath = StaticRelativeFilePathHelper::resolveFromCwd($filePath);

        return new File(
            $relativeFilePath,
            $fileContents,
            $this->fixer,
            $this->skipper,
            $this->sniffMetadataCollector,
            $this->easyCodingStandardStyle
        );
    }
}
