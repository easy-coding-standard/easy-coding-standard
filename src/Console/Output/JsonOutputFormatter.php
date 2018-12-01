<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Nette\Utils\Json;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Error\FileDiff;
use Symplify\PackageBuilder\Console\ShellCode;

final class JsonOutputFormatter implements OutputFormatterInterface
{
    /**
     * @var string
     */
    public const NAME = 'json';

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    public function __construct(
        ErrorAndDiffCollector $errorAndDiffCollector,
        Configuration $configuration,
        EasyCodingStandardStyle $easyCodingStandardStyle
    ) {
        $this->errorAndDiffCollector = $errorAndDiffCollector;
        $this->configuration = $configuration;
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
    }

    public function report(int $processedFilesCount): int
    {
        $errorsArray = [
            'meta' => [
                'version' => $this->configuration->getPrettyVersion(),
                'config' => $this->configuration->getConfigFilePath(),
            ],
            'totals' => [
                'errors' => $this->errorAndDiffCollector->getErrorCount(),
                'diffs' => $this->errorAndDiffCollector->getFileDiffsCount(),
            ],
            'files' => [],
        ];

        /** @var Error[] $errors */
        foreach ($this->errorAndDiffCollector->getErrors() as $file => $errors) {
            foreach ($errors as $error) {
                $errorsArray['files'][$file]['errors'][] = [
                    'line' => $error->getLine(),
                    'message' => $error->getMessage(),
                    'sourceClass' => $error->getSourceClass(),
                ];
            }
        }

        /** @var FileDiff[] $diffs */
        foreach ($this->errorAndDiffCollector->getFileDiffs() as $file => $diffs) {
            foreach ($diffs as $diff) {
                $errorsArray['files'][$file]['diffs'][] = [
                    'diff' => $diff->getDiff(),
                    'appliedCheckers' => $diff->getAppliedCheckers(),
                ];
            }
        }

        $json = Json::encode($errorsArray, Json::PRETTY);

        $this->easyCodingStandardStyle->writeln($json);

        return $errorsArray['totals']['errors'] === 0 ? ShellCode::SUCCESS : ShellCode::ERROR;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
