<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Console\Report\FixReport;

use ECSPrefix20210510\Symfony\Component\Console\Formatter\OutputFormatter;
/**
 * @author Kévin Gomez <contact@kevingomez.fr>
 *
 * @internal
 */
final class CheckstyleReporter implements \PhpCsFixer\Console\Report\FixReport\ReporterInterface
{
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getFormat()
    {
        return 'checkstyle';
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function generate(\PhpCsFixer\Console\Report\FixReport\ReportSummary $reportSummary)
    {
        if (!\extension_loaded('dom')) {
            throw new \RuntimeException('Cannot generate report! `ext-dom` is not available!');
        }
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $checkstyles = $dom->appendChild($dom->createElement('checkstyle'));
        foreach ($reportSummary->getChanged() as $filePath => $fixResult) {
            /** @var \DOMElement $file */
            $file = $checkstyles->appendChild($dom->createElement('file'));
            $file->setAttribute('name', $filePath);
            foreach ($fixResult['appliedFixers'] as $appliedFixer) {
                $error = $this->createError($dom, $appliedFixer);
                $file->appendChild($error);
            }
        }
        $dom->formatOutput = \true;
        return $reportSummary->isDecoratedOutput() ? \ECSPrefix20210510\Symfony\Component\Console\Formatter\OutputFormatter::escape($dom->saveXML()) : $dom->saveXML();
    }
    /**
     * @param string $appliedFixer
     * @return \DOMElement
     */
    private function createError(\DOMDocument $dom, $appliedFixer)
    {
        $appliedFixer = (string) $appliedFixer;
        $error = $dom->createElement('error');
        $error->setAttribute('severity', 'warning');
        $error->setAttribute('source', 'PHP-CS-Fixer.' . $appliedFixer);
        $error->setAttribute('message', 'Found violation(s) of type: ' . $appliedFixer);
        return $error;
    }
}
