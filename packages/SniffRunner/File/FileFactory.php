<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SniffRunner\File;

use PHP_CodeSniffer\Fixer;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\SniffRunner\DataCollector\SniffMetadataCollector;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use ECSPrefix202206\Symplify\Skipper\Skipper\Skipper;
use ECSPrefix202206\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCodingStandard\Tests\SniffRunner\File\FileFactoryTest
 */
final class FileFactory
{
    /**
     * @var \PHP_CodeSniffer\Fixer
     */
    private $fixer;
    /**
     * @var \Symplify\Skipper\Skipper\Skipper
     */
    private $skipper;
    /**
     * @var \Symplify\EasyCodingStandard\SniffRunner\DataCollector\SniffMetadataCollector
     */
    private $sniffMetadataCollector;
    /**
     * @var \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;
    public function __construct(Fixer $fixer, Skipper $skipper, SniffMetadataCollector $sniffMetadataCollector, EasyCodingStandardStyle $easyCodingStandardStyle)
    {
        $this->fixer = $fixer;
        $this->skipper = $skipper;
        $this->sniffMetadataCollector = $sniffMetadataCollector;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }
    public function createFromFileInfo(SmartFileInfo $smartFileInfo) : File
    {
        return new File($smartFileInfo->getRelativeFilePath(), $smartFileInfo->getContents(), $this->fixer, $this->skipper, $this->sniffMetadataCollector, $this->easyCodingStandardStyle);
    }
}
