<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Formatter;

use Nette\Utils\Strings;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Contract\RegexAwareFormatterInterface;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Provider\CurrentParentFileInfoProvider;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;
use Throwable;

abstract class AbstractPHPFormatter implements RegexAwareFormatterInterface
{
    /**
     * @var SmartFileSystem
     */
    protected $smartFileSystem;

    /**
     * @var FixerFileProcessor
     */
    protected $fixerFileProcessor;

    /**
     * @var SniffFileProcessor
     */
    protected $sniffFileProcessor;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var CurrentParentFileInfoProvider
     */
    private $currentParentFileInfoProvider;

    public function __construct(
        SmartFileSystem $smartFileSystem,
        FixerFileProcessor $fixerFileProcessor,
        SniffFileProcessor $sniffFileProcessor,
        Configuration $configuration,
        CurrentParentFileInfoProvider $currentParentFileInfoProvider
    ) {
        $this->smartFileSystem = $smartFileSystem;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->configuration = $configuration;
        $this->currentParentFileInfoProvider = $currentParentFileInfoProvider;
    }

    public function format(SmartFileInfo $fileInfo): string
    {
        $this->currentParentFileInfoProvider->setParentFileInfo($fileInfo);

        return (string) Strings::replace($fileInfo->getContents(), $this->provideRegex(), function ($match): string {
            return $this->fixContentAndPreserveFormatting($match);
        });
    }

    /**
     * @param string[] $match
     */
    private function fixContentAndPreserveFormatting(array $match): string
    {
        return rtrim($match['opening'], PHP_EOL) . PHP_EOL
            . $this->fixContent($match['content'])
            . ltrim($match['closing'], PHP_EOL);
    }

    private function fixContent(string $content): string
    {
        $content = trim($content);
        $key = md5($content);

        /** @var string $temporaryFile */
        $temporaryFile = sys_get_temp_dir() . '/ecs_temp/' . sprintf('php-code-%s.php', $key);

        $hasPreviouslyOpeningPHPTag = true;
        if (! Strings::startsWith($content, '<?php')) {
            $content = '<?php' . PHP_EOL . $content;
            $hasPreviouslyOpeningPHPTag = false;
        }

        $fileContent = $content;

        $this->smartFileSystem->dumpFile($temporaryFile, $fileContent);
        $temporaryFileInfo = new SmartFileInfo($temporaryFile);

        try {
            $this->fixerFileProcessor->processFile($temporaryFileInfo);
            $this->sniffFileProcessor->processFile($temporaryFileInfo);

            $fileContent = $temporaryFileInfo->getContents();
        } catch (Throwable $throwable) {
            // Skipped parsed error when processing php temporaryFile
        } finally {
            // remove temporary temporaryFile
            $this->smartFileSystem->remove($temporaryFile);
        }

        if (! $hasPreviouslyOpeningPHPTag) {
            $fileContent = substr($fileContent, 6);
        }

        return rtrim($fileContent, PHP_EOL) . PHP_EOL;
    }
}
