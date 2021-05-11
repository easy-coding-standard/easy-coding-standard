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

use ECSPrefix20210511\Symfony\Component\Console\Formatter\OutputFormatter;
/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class JsonReporter implements \PhpCsFixer\Console\Report\FixReport\ReporterInterface
{
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getFormat()
    {
        return 'json';
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function generate(\PhpCsFixer\Console\Report\FixReport\ReportSummary $reportSummary)
    {
        $jFiles = [];
        foreach ($reportSummary->getChanged() as $file => $fixResult) {
            $jfile = ['name' => $file];
            if ($reportSummary->shouldAddAppliedFixers()) {
                $jfile['appliedFixers'] = $fixResult['appliedFixers'];
            }
            if (!empty($fixResult['diff'])) {
                $jfile['diff'] = $fixResult['diff'];
            }
            $jFiles[] = $jfile;
        }
        $json = ['files' => $jFiles];
        if (null !== $reportSummary->getTime()) {
            $json['time'] = ['total' => \round($reportSummary->getTime() / 1000, 3)];
        }
        if (null !== $reportSummary->getMemory()) {
            $json['memory'] = \round($reportSummary->getMemory() / 1024 / 1024, 3);
        }
        $json = \json_encode($json);
        return $reportSummary->isDecoratedOutput() ? \ECSPrefix20210511\Symfony\Component\Console\Formatter\OutputFormatter::escape($json) : $json;
    }
}
