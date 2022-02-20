<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SnippetFormatter\Formatter;

use ECSPrefix20220220\Nette\Utils\Strings;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetKind;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileSystem;
use Throwable;
/**
 * @see \Symplify\EasyCodingStandard\Tests\SnippetFormatter\Markdown\MarkdownSnippetFormatterTest
 * @see \Symplify\EasyCodingStandard\Tests\SnippetFormatter\HeredocNowdoc\HereNowDocSnippetFormatterTest
 */
final class SnippetFormatter
{
    /**
     * @see https://regex101.com/r/MJTq5C/1
     * @var string
     */
    private const DECLARE_REGEX = '#(declare\\(strict\\_types\\=1\\)\\;\\n)#ms';
    /**
     * @see https://regex101.com/r/MJTq5C/3
     * @var string
     */
    private const OPENING_TAG_REGEX = '#^\\<\\?php\\n#ms';
    /**
     * @see https://regex101.com/r/MJTq5C/3
     * @var string
     */
    private const OPENING_TAG_HERENOWDOC_REGEX = '#^\\<\\?php\\n#ms';
    /**
     * @var string
     */
    private const CONTENT = 'content';
    /**
     * @var string
     */
    private const OPENING = 'opening';
    /**
     * @var string
     */
    private const CLOSING = 'closing';
    /**
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    private $smartFileSystem;
    /**
     * @var \Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor
     */
    private $fixerFileProcessor;
    /**
     * @var \Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor
     */
    private $sniffFileProcessor;
    /**
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider
     */
    private $currentParentFileInfoProvider;
    public function __construct(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem, \Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor $fixerFileProcessor, \Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor $sniffFileProcessor, \Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider $currentParentFileInfoProvider)
    {
        $this->smartFileSystem = $smartFileSystem;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->currentParentFileInfoProvider = $currentParentFileInfoProvider;
    }
    public function format(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $fileInfo, string $snippetRegex, string $kind, \Symplify\EasyCodingStandard\ValueObject\Configuration $configuration) : string
    {
        $this->currentParentFileInfoProvider->setParentFileInfo($fileInfo);
        return \ECSPrefix20220220\Nette\Utils\Strings::replace($fileInfo->getContents(), $snippetRegex, function ($match) use($kind, $configuration) : string {
            if (\strpos($match[self::CONTENT], '-----') !== \false) {
                // do nothing
                return $match[self::OPENING] . $match[self::CONTENT] . $match[self::CLOSING];
            }
            return $this->fixContentAndPreserveFormatting($match, $kind, $configuration);
        });
    }
    /**
     * @param string[] $match
     */
    private function fixContentAndPreserveFormatting(array $match, string $kind, \Symplify\EasyCodingStandard\ValueObject\Configuration $configuration) : string
    {
        return \str_replace(\PHP_EOL, '', $match[self::OPENING]) . \PHP_EOL . $this->fixContent($match[self::CONTENT], $kind, $configuration) . \str_replace(\PHP_EOL, '', $match[self::CLOSING]);
    }
    private function fixContent(string $content, string $kind, \Symplify\EasyCodingStandard\ValueObject\Configuration $configuration) : string
    {
        $temporaryFilePath = $this->createTemporaryFilePath($content);
        if (\strncmp(\trim($content), '<?php', \strlen('<?php')) !== 0) {
            $content = '<?php' . \PHP_EOL . $content;
        }
        $fileContent = \ltrim($content, \PHP_EOL);
        $this->smartFileSystem->dumpFile($temporaryFilePath, $fileContent);
        $temporaryFileInfo = new \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo($temporaryFilePath);
        try {
            $this->fixerFileProcessor->processFile($temporaryFileInfo, $configuration);
            $this->sniffFileProcessor->processFile($temporaryFileInfo, $configuration);
            $changedFileContent = $temporaryFileInfo->getContents();
        } catch (\Throwable $exception) {
            // Skipped parsed error when processing php temporaryFile
            $changedFileContent = $fileContent;
        } finally {
            // remove temporary temporaryFile
            $this->smartFileSystem->remove($temporaryFilePath);
        }
        $changedFileContent = \rtrim($changedFileContent, \PHP_EOL) . \PHP_EOL;
        if ($kind === \Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetKind::MARKDOWN) {
            return $this->resolveMarkdownFileContent($changedFileContent);
        }
        return \ECSPrefix20220220\Nette\Utils\Strings::replace($changedFileContent, self::OPENING_TAG_HERENOWDOC_REGEX, '$1');
    }
    /**
     * It does not have any added value and only clutters the output
     */
    private function removeOpeningTagAndStrictTypes(string $content) : string
    {
        $content = \ECSPrefix20220220\Nette\Utils\Strings::replace($content, self::DECLARE_REGEX, '');
        return $this->removeOpeningTag($content);
    }
    private function createTemporaryFilePath(string $content) : string
    {
        $key = \md5($content);
        $fileName = \sprintf('php-code-%s.php', $key);
        return \sys_get_temp_dir() . \DIRECTORY_SEPARATOR . 'ecs_temp' . \DIRECTORY_SEPARATOR . $fileName;
    }
    private function removeOpeningTag(string $fileContent) : string
    {
        return \ECSPrefix20220220\Nette\Utils\Strings::replace($fileContent, self::OPENING_TAG_REGEX, '$1');
    }
    private function resolveMarkdownFileContent(string $fileContent) : string
    {
        $fileContent = \ltrim($fileContent, \PHP_EOL);
        $fileContent = $this->removeOpeningTagAndStrictTypes($fileContent);
        return \ltrim($fileContent);
    }
}
