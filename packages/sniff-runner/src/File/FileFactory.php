<?php

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use PHP_CodeSniffer\Fixer;
use Symplify\EasyCodingStandard\Application\AppliedCheckersCollector;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\Skipper\Skipper\Skipper;
use Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCodingStandard\SniffRunner\Tests\File\FileFactoryTest
 */
final class FileFactory
{
    /**
     * @var Fixer
     */
    private $fixer;
    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;
    /**
     * @var Skipper
     */
    private $skipper;
    /**
     * @var AppliedCheckersCollector
     */
    private $appliedCheckersCollector;
    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;
    /**
     * @param \PHP_CodeSniffer\Fixer $fixer
     * @param \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector $errorAndDiffCollector
     * @param \Symplify\Skipper\Skipper\Skipper $skipper
     * @param \Symplify\EasyCodingStandard\Application\AppliedCheckersCollector $appliedCheckersCollector
     * @param \Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle $easyCodingStandardStyle
     */
    public function __construct($fixer, $errorAndDiffCollector, $skipper, $appliedCheckersCollector, $easyCodingStandardStyle)
    {
        $this->fixer = $fixer;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->skipper = $skipper;
        $this->appliedCheckersCollector = $appliedCheckersCollector;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }
    /**
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     * @return \Symplify\EasyCodingStandard\SniffRunner\ValueObject\File
     */
    public function createFromFileInfo($smartFileInfo)
    {
        return new File($smartFileInfo->getRelativeFilePath(), $smartFileInfo->getContents(), $this->fixer, $this->errorAndDiffCollector, $this->skipper, $this->appliedCheckersCollector, $this->easyCodingStandardStyle);
    }
}
