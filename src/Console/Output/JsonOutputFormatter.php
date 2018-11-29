<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Nette\Utils\Json;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\PackageBuilder\Console\ShellCode;

final class JsonOutputFormatter implements OutputFormatterInterface
{
    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    public function __construct(
        EasyCodingStandardStyle $easyCodingStandardStyle,
        Configuration $configuration,
        ErrorAndDiffCollector $errorAndDiffCollector
    ) {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->configuration = $configuration;
        $this->errorAndDiffCollector = $errorAndDiffCollector;
    }

    public function report(int $processedFilesCount): int
    {
        $errorsArray = [
            'totals' => [
                'errors' => $this->errorAndDiffCollector->getErrorCount(),
            ],
            'errors' => $this->errorAndDiffCollector->getErrors(),
        ];

        /** @var Error[] $errors */
        foreach ($this->errorAndDiffCollector->getErrors() as $file => $errors) {
            foreach ($errors as $error) {
                $errorsArray['errors'][$file] = [
                    'line' => $error->getLine(),
                    'message' => $error->getMessage(),
                    'sourceClass' => $error->getSourceClass(),
                ];
            }
        }

        $this->easyCodingStandardStyle->writeln('');
        $this->easyCodingStandardStyle->writeln(Json::encode($errorsArray, Json::PRETTY));

        return $errorsArray['totals']['errors'] === 0 ? ShellCode::SUCCESS : ShellCode::ERROR;
    }
}
