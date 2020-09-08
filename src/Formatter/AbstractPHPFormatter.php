<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Formatter;

use Nette\Utils\Strings;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;
use Throwable;

abstract class AbstractPHPFormatter
{
    /**
     * Regex to be overridden in derived classes
     * @var string
     */
    protected const PHP_CODE_SNIPPET = '##';

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

    public function __construct(
        SmartFileSystem $smartFileSystem,
        FixerFileProcessor $fixerFileProcessor,
        SniffFileProcessor $sniffFileProcessor,
        Configuration $configuration
    ) {
        $this->smartFileSystem = $smartFileSystem;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->configuration = $configuration;
    }

    public function format(SmartFileInfo $fileInfo): string
    {
        // enable fixing
        $this->configuration->resolveFromArray(['isFixer' => true]);

        return (string) Strings::replace(
            $fileInfo->getContents(),
            static::PHP_CODE_SNIPPET,
            function ($match): string {
                $fixedContent = rtrim($this->fixContent($match['content']), PHP_EOL) . PHP_EOL;
                return rtrim($match['opening'], PHP_EOL) . PHP_EOL . $fixedContent . $match['closing'];
            }
        );
    }

    protected function fixContent(string $content): string
    {
        $key = md5($content);

        /** @var string $file */
        $file = sprintf('php-code-%s.php', $key);

        $hasPreviouslyOpeningPHPTag = true;
        if (! Strings::startsWith($content, '<?php')) {
            $content = '<?php' . PHP_EOL . $content;
            $hasPreviouslyOpeningPHPTag = false;
        }

        $fileContent = $content;

        $this->smartFileSystem->dumpFile($file, $fileContent);

        $fileInfo = new SmartFileInfo($file);
        try {
            $this->fixerFileProcessor->processFile($fileInfo);
            $this->sniffFileProcessor->processFile($fileInfo);

            $fileContent = $fileInfo->getContents();
        } catch (Throwable $throwable) {
            // Skipped parsed error when processing php file
        } finally {
            $this->smartFileSystem->remove($file);
        }

        if (! $hasPreviouslyOpeningPHPTag) {
            $fileContent = substr($fileContent, 6);
        }

        return $fileContent;
    }
}
