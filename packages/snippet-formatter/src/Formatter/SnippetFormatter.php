<?php

namespace Symplify\EasyCodingStandard\SnippetFormatter\Formatter;

use ECSPrefix20210507\Nette\Utils\Strings;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;
use Throwable;
/**
 * @see \Symplify\EasyCodingStandard\SnippetFormatter\Tests\Markdown\MarkdownSnippetFormatterTest
 * @see \Symplify\EasyCodingStandard\SnippetFormatter\Tests\HeredocNowdoc\HereNowDocSnippetFormatterTest
 */
final class SnippetFormatter
{
    /**
     * @see https://regex101.com/r/MJTq5C/1
     * @var string
     */
    const DECLARE_REGEX = '#(declare\\(strict\\_types\\=1\\)\\;\\n)#ms';
    /**
     * @see https://regex101.com/r/MJTq5C/3
     * @var string
     */
    const OPENING_TAG_REGEX = '#^\\<\\?php\\n#ms';
    /**
     * @see https://regex101.com/r/MJTq5C/3
     * @var string
     */
    const OPENING_TAG_HERENOWDOC_REGEX = '#^\\<\\?php\\n#ms';
    /**
     * @var string
     */
    const CONTENT = 'content';
    /**
     * @var string
     */
    const OPENING = 'opening';
    /**
     * @var string
     */
    const CLOSING = 'closing';
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;
    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;
    /**
     * @var CurrentParentFileInfoProvider
     */
    private $currentParentFileInfoProvider;
    /**
     * @var bool
     */
    private $isPhp73OrAbove = \false;
    /**
     * @param \Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem
     * @param \Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor $fixerFileProcessor
     * @param \Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor $sniffFileProcessor
     * @param \Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider $currentParentFileInfoProvider
     */
    public function __construct($smartFileSystem, $fixerFileProcessor, $sniffFileProcessor, $currentParentFileInfoProvider)
    {
        $this->smartFileSystem = $smartFileSystem;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->currentParentFileInfoProvider = $currentParentFileInfoProvider;
        $this->isPhp73OrAbove = \PHP_VERSION_ID >= 70300;
    }
    /**
     * @param \Symplify\SmartFileSystem\SmartFileInfo $fileInfo
     * @param string $snippetRegex
     * @param string $kind
     * @return string
     */
    public function format($fileInfo, $snippetRegex, $kind)
    {
        $this->currentParentFileInfoProvider->setParentFileInfo($fileInfo);
        return Strings::replace($fileInfo->getContents(), $snippetRegex, function ($match) use($kind) : string {
            if (Strings::contains($match[self::CONTENT], '-----')) {
                // do nothing
                return $match[self::OPENING] . $match[self::CONTENT] . $match[self::CLOSING];
            }
            return $this->fixContentAndPreserveFormatting($match, $kind);
        });
    }
    /**
     * @param string[] $match
     * @param string $kind
     * @return string
     */
    private function fixContentAndPreserveFormatting(array $match, $kind)
    {
        if ($this->isPhp73OrAbove) {
            return \str_replace(\PHP_EOL, '', $match[self::OPENING]) . \PHP_EOL . $this->fixContent($match[self::CONTENT], $kind) . \str_replace(\PHP_EOL, '', $match[self::CLOSING]);
        }
        return \rtrim($match[self::OPENING], \PHP_EOL) . \PHP_EOL . $this->fixContent($match[self::CONTENT], $kind) . \ltrim($match[self::CLOSING], \PHP_EOL);
    }
    /**
     * @param string $content
     * @param string $kind
     * @return string
     */
    private function fixContent($content, $kind)
    {
        $content = $this->isPhp73OrAbove ? $content : \trim($content);
        $temporaryFilePath = $this->createTemporaryFilePath($content);
        if (!Strings::startsWith($this->isPhp73OrAbove ? \trim($content) : $content, '<?php')) {
            $content = '<?php' . \PHP_EOL . $content;
        }
        $fileContent = $this->isPhp73OrAbove ? \ltrim($content, \PHP_EOL) : $content;
        $this->smartFileSystem->dumpFile($temporaryFilePath, $fileContent);
        $temporaryFileInfo = new SmartFileInfo($temporaryFilePath);
        try {
            $this->fixerFileProcessor->processFile($temporaryFileInfo);
            $this->sniffFileProcessor->processFile($temporaryFileInfo);
            $fileContent = $temporaryFileInfo->getContents();
        } catch (Throwable $throwable) {
            // Skipped parsed error when processing php temporaryFile
        } finally {
            // remove temporary temporaryFile
            $this->smartFileSystem->remove($temporaryFilePath);
        }
        $fileContent = \rtrim($fileContent, \PHP_EOL) . \PHP_EOL;
        if ($kind === 'markdown') {
            $fileContent = \ltrim($fileContent, \PHP_EOL);
            $fileContent = $this->removeOpeningTagAndStrictTypes($fileContent);
            return \ltrim($fileContent);
        }
        return Strings::replace($fileContent, self::OPENING_TAG_HERENOWDOC_REGEX, '$1');
    }
    /**
     * It does not have any added value and only clutters the output
     * @param string $content
     * @return string
     */
    private function removeOpeningTagAndStrictTypes($content)
    {
        $content = Strings::replace($content, self::DECLARE_REGEX, '');
        return $this->removeOpeningTag($content);
    }
    /**
     * @param string $content
     * @return string
     */
    private function createTemporaryFilePath($content)
    {
        $key = \md5($content);
        $fileName = \sprintf('php-code-%s.php', $key);
        return \sys_get_temp_dir() . \DIRECTORY_SEPARATOR . 'ecs_temp' . \DIRECTORY_SEPARATOR . $fileName;
    }
    /**
     * @param string $fileContent
     * @return string
     */
    private function removeOpeningTag($fileContent)
    {
        return Strings::replace($fileContent, self::OPENING_TAG_REGEX, '$1');
    }
}
