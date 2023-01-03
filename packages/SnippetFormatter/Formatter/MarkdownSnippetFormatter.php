<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SnippetFormatter\Formatter;

use ECSPrefix202301\Nette\Utils\FileSystem;
use ECSPrefix202301\Nette\Utils\Strings;
use SplFileInfo;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use ECSPrefix202301\Symplify\SmartFileSystem\SmartFileInfo;
use Throwable;
/**
 * @see \Symplify\EasyCodingStandard\Tests\SnippetFormatter\Markdown\MarkdownSnippetFormatterTest
 */
final class MarkdownSnippetFormatter
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
     * @readonly
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $fileSystem;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor
     */
    private $fixerFileProcessor;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor
     */
    private $sniffFileProcessor;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\SnippetFormatter\Provider\CurrentParentFileInfoProvider
     */
    private $currentParentFileInfoProvider;
    public function __construct(\ECSPrefix202301\Symfony\Component\Filesystem\Filesystem $fileSystem, FixerFileProcessor $fixerFileProcessor, SniffFileProcessor $sniffFileProcessor, CurrentParentFileInfoProvider $currentParentFileInfoProvider)
    {
        $this->fileSystem = $fileSystem;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->currentParentFileInfoProvider = $currentParentFileInfoProvider;
    }
    public function format(SplFileInfo $fileInfo, Configuration $configuration) : string
    {
        $this->currentParentFileInfoProvider->setParentFileInfo($fileInfo);
        $fileContents = FileSystem::read($fileInfo->getRealPath());
        return Strings::replace($fileContents, SnippetPattern::MARKDOWN_PHP_SNIPPET_REGEX, function ($match) use($configuration) : string {
            if (\strpos((string) $match[self::CONTENT], '-----') !== \false) {
                // do nothing
                return $match[self::OPENING] . $match[self::CONTENT] . $match[self::CLOSING];
            }
            return $this->fixContentAndPreserveFormatting($match, $configuration);
        });
    }
    /**
     * @param string[] $match
     */
    private function fixContentAndPreserveFormatting(array $match, Configuration $configuration) : string
    {
        return \str_replace(\PHP_EOL, '', $match[self::OPENING]) . \PHP_EOL . $this->fixContent($match[self::CONTENT], $configuration) . \str_replace(\PHP_EOL, '', $match[self::CLOSING]);
    }
    private function fixContent(string $content, Configuration $configuration) : string
    {
        $temporaryFilePath = $this->createTemporaryFilePath($content);
        if (\strncmp(\trim($content), '<?php', \strlen('<?php')) !== 0) {
            $content = '<?php' . \PHP_EOL . $content;
        }
        $fileContent = \ltrim($content, \PHP_EOL);
        $this->fileSystem->dumpFile($temporaryFilePath, $fileContent);
        $temporaryFileInfo = new SmartFileInfo($temporaryFilePath);
        try {
            $this->fixerFileProcessor->processFile($temporaryFileInfo, $configuration);
            $this->sniffFileProcessor->processFile($temporaryFileInfo, $configuration);
            $changedFileContent = $temporaryFileInfo->getContents();
        } catch (Throwable $exception) {
            // Skipped parsed error when processing php temporaryFile
            $changedFileContent = $fileContent;
        } finally {
            // remove temporary temporaryFile
            $this->fileSystem->remove($temporaryFilePath);
        }
        $changedFileContent = \rtrim($changedFileContent, \PHP_EOL) . \PHP_EOL;
        return $this->resolveMarkdownFileContent($changedFileContent);
    }
    /**
     * It does not have any added value and only clutters the output
     */
    private function removeOpeningTagAndStrictTypes(string $content) : string
    {
        $content = Strings::replace($content, self::DECLARE_REGEX, '');
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
        return Strings::replace($fileContent, self::OPENING_TAG_REGEX, '$1');
    }
    private function resolveMarkdownFileContent(string $fileContent) : string
    {
        $fileContent = \ltrim($fileContent, \PHP_EOL);
        $fileContent = $this->removeOpeningTagAndStrictTypes($fileContent);
        return \ltrim($fileContent);
    }
}
