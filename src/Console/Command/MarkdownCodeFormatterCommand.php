<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use ReflectionProperty;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Configuration\Exception\NoMarkdownFileException;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class MarkdownCodeFormatterCommand extends Command
{
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
     * @var Configuration
     */
    private $configuration;

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    public function __construct(
        SmartFileSystem $smartFileSystem,
        FixerFileProcessor $fixerFileProcessor,
        SniffFileProcessor $sniffFileProcessor,
        Configuration $configuration,
        EasyCodingStandardStyle $easyCodingStandardStyle
    ) {
        $this->smartFileSystem = $smartFileSystem;
        $this->fixerFileProcessor = $fixerFileProcessor;
        $this->sniffFileProcessor = $sniffFileProcessor;
        $this->configuration = $configuration;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('markdown-code-format');
        $this->setDescription('Format markdown code');
        $this->addArgument('markdown-file', InputArgument::REQUIRED, 'The markdown file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $markdownFile */
        $markdownFile = $input->getArgument('markdown-file');
        if (! file_exists($markdownFile)) {
            throw new NoMarkdownFileException(sprintf('Markdown file %s not found', $markdownFile));
        }

        $this->configuration->resolveFromArray(['isFixer' => true]);

        $r = new ReflectionProperty($this->fixerFileProcessor, 'configuration');
        $r->setAccessible(true);
        $r->setValue($this->fixerFileProcessor, $this->configuration);

        $r = new ReflectionProperty($this->sniffFileProcessor, 'configuration');
        $r->setAccessible(true);
        $r->setValue($this->sniffFileProcessor, $this->configuration);

        /** @var string $content */
        $content = file_get_contents($markdownFile);
        $tempContent = $content;
        // @see https://regex101.com/r/4YUIu1/1
        preg_match_all('#\`\`\`php\s+([^\`\`\`]+)\s+\`\`\`#', $content, $matches);

        if ($matches[1] === []) {
            $this->easyCodingStandardStyle->success('No php code found in the markdown');
            return ShellCode::SUCCESS;
        }

        $fixedContents = [];
        foreach ($matches[1] as $key => $match) {
            /** @var string $file */
            $file = sprintf('php-code-%s.php', $key);
            $match = ltrim($match, '<?php');
            $match = '<?php' . PHP_EOL . $match;
            $this->smartFileSystem->dumpFile($file, $match);

            $fileInfo = new SmartFileInfo($file);
            $this->fixerFileProcessor->processFile($fileInfo);

            $fileInfo = new SmartFileInfo($file);
            $this->sniffFileProcessor->processFile($fileInfo);

            /** @var string $fileContent */
            $fileContent = file_get_contents($file);
            $fixedContents[] = ltrim($fileContent, '<?php' . PHP_EOL);
        }

        foreach (array_keys($fixedContents) as $key) {
            $content = preg_replace_callback(
                '#\\`\\`\\`php\\s+([^\\`\\`\\`]+)\\s+\\`\\`\\`#',
                function () use ($fixedContents): string {
                    static $key = 0;

                    $result = '```php' . PHP_EOL . '<?php' . PHP_EOL . ltrim($fixedContents[$key], ' ') . '```';
                    $key++;

                    return $result;
                },
                (string) $content
            );

            /** @var string $file */
            $file = sprintf('php-code-%s.php', $key);
            $this->smartFileSystem->remove($file);
        }

        $this->smartFileSystem->dumpFile($markdownFile, (string) $content);

        if ($tempContent === $content) {
            $this->easyCodingStandardStyle->success('php code in markdown already follow coding standard');
        } else {
            $this->easyCodingStandardStyle->success('php code in markdown has been fixed to follow coding standard');
        }

        return ShellCode::SUCCESS;
    }
}
