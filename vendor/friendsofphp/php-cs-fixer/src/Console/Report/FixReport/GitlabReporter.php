<?php

declare (strict_types=1);
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

use ECSPrefix202312\SebastianBergmann\Diff\Chunk;
use ECSPrefix202312\SebastianBergmann\Diff\Parser;
use ECSPrefix202312\Symfony\Component\Console\Formatter\OutputFormatter;
/**
 * Generates a report according to gitlabs subset of codeclimate json files.
 *
 * @see https://github.com/codeclimate/platform/blob/master/spec/analyzers/SPEC.md#data-types
 *
 * @author Hans-Christian Otto <c.otto@suora.com>
 *
 * @internal
 */
final class GitlabReporter implements \PhpCsFixer\Console\Report\FixReport\ReporterInterface
{
    /**
     * @var \SebastianBergmann\Diff\Parser
     */
    private $diffParser;
    public function __construct()
    {
        $this->diffParser = new Parser();
    }
    public function getFormat() : string
    {
        return 'gitlab';
    }
    /**
     * Process changed files array. Returns generated report.
     */
    public function generate(\PhpCsFixer\Console\Report\FixReport\ReportSummary $reportSummary) : string
    {
        $report = [];
        foreach ($reportSummary->getChanged() as $fileName => $change) {
            $diffs = $this->diffParser->parse($change['diff']);
            $firstChunk = isset($diffs[0]) ? $diffs[0]->getChunks() : [];
            $firstChunk = \array_shift($firstChunk);
            foreach ($change['appliedFixers'] as $fixerName) {
                $report[] = ['check_name' => $fixerName, 'description' => $fixerName, 'categories' => ['Style'], 'fingerprint' => \md5($fileName . $fixerName), 'severity' => 'minor', 'location' => ['path' => $fileName, 'lines' => ['begin' => $firstChunk instanceof Chunk ? $firstChunk->getStart() : 0, 'end' => $firstChunk instanceof Chunk ? $firstChunk->getStartRange() : 0]]];
            }
        }
        $jsonString = \json_encode($report, 0);
        if (\json_last_error() !== \JSON_ERROR_NONE) {
            throw new \Exception(\json_last_error_msg());
        }
        return $reportSummary->isDecoratedOutput() ? OutputFormatter::escape($jsonString) : $jsonString;
    }
}
