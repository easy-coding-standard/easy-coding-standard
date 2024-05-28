<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use DOMDocument;
use DOMElement;
use RuntimeException;
use Symplify\EasyCodingStandard\Console\ExitCode;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;

/**
 * @see \Symplify\EasyCodingStandard\Tests\Console\Output\JsonOutputFormatterTest
 */
final readonly class CheckstyleOutputFormatter implements OutputFormatterInterface
{
    public function __construct(
        private EasyCodingStandardStyle $easyCodingStandardStyle,
        private ExitCodeResolver $exitCodeResolver
    ) {
    }

    /**
     * @return ExitCode::*
     */
    public function report(ErrorAndDiffResult $errorAndDiffResult, Configuration $configuration): int
    {
        $checkstyleContent = $this->createCheckstyleContent($errorAndDiffResult);
        $this->easyCodingStandardStyle->writeln($checkstyleContent);

        return $this->exitCodeResolver->resolve($errorAndDiffResult, $configuration);
    }

    public static function getName(): string
    {
        return 'checkstyle';
    }

    public static function hasSupportForProgressBars(): bool
    {
        return false;
    }

    /**
     * @api
     */
    public function createCheckstyleContent(ErrorAndDiffResult $errorAndDiffResult): string
    {
        if (! \extension_loaded('dom')) {
            throw new RuntimeException('Cannot generate report! `ext-dom` is not available!');
        }

        $domDocument = new DOMDocument('1.0', 'UTF-8');

        /** @var DOMElement $checkstyleElement */
        $checkstyleElement = $domDocument->appendChild($domDocument->createElement('checkstyle'));

        foreach ($errorAndDiffResult->getFileDiffs() as $fileDiff) {
            /** @var DOMElement $file */
            $file = $checkstyleElement->appendChild($domDocument->createElement('file'));
            $file->setAttribute('name', $fileDiff->getRelativeFilePath());

            foreach ($fileDiff->getAppliedCheckers() as $appliedChecker) {
                $errorElement = $this->createError($domDocument, $appliedChecker);
                $file->appendChild($errorElement);
            }
        }

        $domDocument->formatOutput = true;

        return (string) $domDocument->saveXML();
    }

    private function createError(DOMDocument $domDocument, string $appliedChecker): DOMElement
    {
        $domElement = $domDocument->createElement('error');
        $domElement->setAttribute('severity', 'warning');
        $domElement->setAttribute('source', 'EasyCodingStandard.' . $appliedChecker);
        $domElement->setAttribute('message', 'Found violation(s) of type: ' . $appliedChecker);

        return $domElement;
    }
}
