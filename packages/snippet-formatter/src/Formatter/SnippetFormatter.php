<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Formatter;

use Nette\Utils\Strings;
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
    private const DECLARE_REGEX = '#(declare\(strict\_types\=1\)\;\n)#ms';

    /**
     * @see https://regex101.com/r/MJTq5C/3
     * @var string
     */
    private const OPENING_TAG_REGEX = '#^\<\?php\n#ms';

    /**
     * @see https://regex101.com/r/MJTq5C/3
     * @var string
     */
    private const OPENING_TAG_HERENOWDOC_REGEX = '#^\<\?php\n#ms';

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
    private $isPhp73OrAbove;

    public function __construct(
        SmartFileSystem $smartFileSystem,
        FixerFileProcessor $fixerFileProcessor,
        SniffFileProcessor $sniffFileProcessor,
        CurrentParentFileInfoProvider $currentParentFileInfoProvider
    ) {
        $this->smartFileSystem = $smartFileSystem;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->currentParentFileInfoProvider = $currentParentFileInfoProvider;
        $this->isPhp73OrAbove = PHP_VERSION_ID >= 70300;
    }

    public function format(SmartFileInfo $fileInfo, string $snippetRegex, string $kind): string
    {
        $this->currentParentFileInfoProvider->setParentFileInfo($fileInfo);

        return (string) Strings::replace($fileInfo->getContents(), $snippetRegex, function ($match) use (
            $kind
        ): string {
            if (Strings::contains($match['content'], '-----')) {
                // do nothing
                return $match['opening'] . $match['content'] . $match['closing'];
            }

            return $this->fixContentAndPreserveFormatting($match, $kind);
        });
    }

    /**
     * @param string[] $match
     */
    private function fixContentAndPreserveFormatting(array $match, string $kind): string
    {
        if ($this->isPhp73OrAbove) {
            return str_replace(PHP_EOL, '', $match['opening']) . PHP_EOL
                . $this->fixContent($match['content'], $kind)
                . str_replace(PHP_EOL, '', $match['closing']);
        }

        return rtrim($match['opening'], PHP_EOL) . PHP_EOL
            . $this->fixContent($match['content'], $kind)
            . ltrim($match['closing'], PHP_EOL);
    }

    private function fixContent(string $content, string $kind): string
    {
        $content = $this->isPhp73OrAbove ? $content : trim($content);
        $temporaryFilePath = $this->createTemporaryFilePath($content);

        if (! Strings::startsWith($this->isPhp73OrAbove ? trim($content) : $content, '<?php')) {
            $content = '<?php' . PHP_EOL . $content;
        }

        $fileContent = $this->isPhp73OrAbove ? ltrim($content, PHP_EOL) : $content;

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

        $fileContent = rtrim($fileContent, PHP_EOL) . PHP_EOL;

        if ($kind === 'markdown') {
            $fileContent = ltrim($fileContent, PHP_EOL);

            $fileContent = $this->removeOpeningTagAndStrictTypes($fileContent);

            return ltrim($fileContent);
        }

        return Strings::replace($fileContent, self::OPENING_TAG_HERENOWDOC_REGEX, '$1');
    }

    /**
     * It does not have any added value and only clutters the output
     */
    private function removeOpeningTagAndStrictTypes(string $content): string
    {
        $content = Strings::replace($content, self::DECLARE_REGEX, '');

        return $this->removeOpeningTag($content);
    }

    private function createTemporaryFilePath(string $content): string
    {
        $key = md5($content);
        $fileName = sprintf('php-code-%s.php', $key);

        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ecs_temp' . DIRECTORY_SEPARATOR . $fileName;
    }

    private function removeOpeningTag(string $fileContent): string
    {
        return Strings::replace($fileContent, self::OPENING_TAG_REGEX, '$1');
    }
}
