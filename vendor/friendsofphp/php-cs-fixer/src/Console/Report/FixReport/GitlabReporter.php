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

use PhpCsFixer\Console\Application;
use ECSPrefix202408\SebastianBergmann\Diff\Chunk;
use ECSPrefix202408\SebastianBergmann\Diff\Diff;
use ECSPrefix202408\SebastianBergmann\Diff\Parser;
use ECSPrefix202408\Symfony\Component\Console\Formatter\OutputFormatter;
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
        $about = Application::getAbout();
        $report = [];
        foreach ($reportSummary->getChanged() as $fileName => $change) {
            foreach ($change['appliedFixers'] as $fixerName) {
                $report[] = ['check_name' => 'PHP-CS-Fixer.' . $fixerName, 'description' => 'PHP-CS-Fixer.' . $fixerName . ' by ' . $about, 'categories' => ['Style'], 'fingerprint' => \md5($fileName . $fixerName), 'severity' => 'minor', 'location' => ['path' => $fileName, 'lines' => self::getLines($this->diffParser->parse($change['diff']))]];
            }
        }
        $jsonString = \json_encode($report, 0);
        if (\json_last_error() !== \JSON_ERROR_NONE) {
            throw new \Exception(\json_last_error_msg());
        }
        return $reportSummary->isDecoratedOutput() ? OutputFormatter::escape($jsonString) : $jsonString;
    }
    /**
     * @param list<Diff> $diffs
     *
     * @return array{begin: int, end: int}
     */
    private static function getLines(array $diffs) : array
    {
        if (isset($diffs[0])) {
            $firstDiff = $diffs[0];
            $firstChunk = \Closure::bind(static function (Diff $diff) {
                return \array_shift($diff->chunks);
            }, null, $firstDiff)($firstDiff);
            if ($firstChunk instanceof Chunk) {
                return \Closure::bind(static function (Chunk $chunk) : array {
                    return ['begin' => $chunk->start, 'end' => $chunk->startRange];
                }, null, $firstChunk)($firstChunk);
            }
        }
        return ['begin' => 0, 'end' => 0];
    }
}
