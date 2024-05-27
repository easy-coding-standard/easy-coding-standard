<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use SebastianBergmann\Diff\Chunk;
use SebastianBergmann\Diff\Line;
use SebastianBergmann\Diff\Parser as DiffParser;
use Symplify\EasyCodingStandard\Console\ExitCode;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use function array_map as map;
use function array_merge as merge;

/**
 * Generates a JSON file containing the Gitlab-supported variant of
 * "Code Climate" issues for all unresolved errors.
 *
 * This is compatible with the specs of:
 *   - Code Climate: ^0.3.1
 *   - Gitlab: ^16.11 || ^17
 *
 * Applied diffs are NOT reported, but unapplied diffs ARE. This allows
 * our users to choose between a CI/CD workflow where `--fix` is used to
 * automatically commit trivial corrections OR one where any errors, even
 * resolvable, cause the pipeline to fail.
 *
 * Unfortunately, because of the way sniffs apply fixes without provenance data
 * and with cascading effects, multiple major refactors would be required in
 * order to accurately report exactly which fixes apply to exactly which lines.
 *
 * Finally, all reported errors will be marked as "blocker" severity.
 *
 * As a warning to future maintainers, I believe Gitlab's documentation may be
 * slightly wrong. It's not a subset of the Code Climate format as insinuated,
 * since it changes some fields from optional to required.
 *
 * @see https://docs.gitlab.com/ee/ci/testing/code_quality.html#implement-a-custom-tool
 * @see https://github.com/codeclimate/platform/blob/master/spec/analyzers/SPEC.md#data-types
 *
 * @phpstan-type GitlabIssue array{
 *     type: 'issue',
 *     description: string,
 *     check_name: string,
 *     fingerprint: string,
 *     severity: 'blocker',
 *     categories: array{'Style'},
 *     remediation_points?: int,
 *     location: array{
 *         path: string,
 *         lines: array{
 *             begin: int,
 *             end: int,
 *         },
 *     },
 * }
 */
final readonly class GitlabOutputFormatter implements OutputFormatterInterface
{
    public function __construct(
        private readonly EasyCodingStandardStyle $easyCodingStandardStyle,
        private readonly ExitCodeResolver $exitCodeResolver,
        private readonly DiffParser $diffParser,
    ) {
    }

    public static function getName(): string
    {
        return 'gitlab';
    }

    public static function hasSupportForProgressBars(): bool
    {
        return false;
    }

    /**
     * @return ExitCode::*
     */
    public function report(ErrorAndDiffResult $results, Configuration $config): int
    {
        $reportedQualityIssues = (! $config->isFixer() && $config->shouldShowDiffs())
            ? merge(
                $this->generateIssuesForErrors($results->getErrors()),
                $this->generateIssuesForFixes($results->getFileDiffs()),
            )
            : $this->generateIssuesForErrors($results->getErrors());

        $output = $this->encode($reportedQualityIssues);

        $this->easyCodingStandardStyle->writeln($output);
        return $this->exitCodeResolver->resolve($results, $config);
    }

    /**
     * @param CodingStandardError[] $errors
     * @return GitlabIssue[]
     */
    private function generateIssuesForErrors(array $errors): array
    {
        return map(
            fn (CodingStandardError $error) => [
                'type' => 'issue',
                'description' => $error->getMessage(),
                'check_name' => $error->getCheckerClass(),
                'fingerprint' => $this->generateFingerprint(
                    $error->getCheckerClass(),
                    $error->getMessage(),
                    $error->getRelativeFilePath(),
                ),
                'severity' => 'blocker',
                'categories' => ['Style'],
                'location' => [
                    'path' => $error->getRelativeFilePath(),
                    'lines' => [
                        'begin' => $error->getLine(),
                        'end' => $error->getLine(),
                    ],
                ],
            ],
            $errors,
        );
    }

    /**
     * Reports each chunk of changes as a separate issue.
     *
     * @param FileDiff[] $diffs
     * @return GitlabIssue[]
     */
    private function generateIssuesForFixes(array $diffs): array
    {
        return merge(
            ...map(
                fn (FileDiff $diff) => map(
                    fn (Chunk $chunk) => $this->generateIssueForChunk($diff, $chunk),
                    $this->diffParser->parse($diff->getDiff())[0]->chunks(),
                ),
                $diffs,
            ),
        );
    }

    /**
     * @return GitlabIssue
     */
    private function generateIssueForChunk(FileDiff $diff, Chunk $chunk): array
    {
        $checkersAsFqcns = implode(',', $diff->getAppliedCheckers());
        $checkersAsClasses = implode(', ', map(
            fn (string $checker) => preg_replace('/.*\\\/', '', $checker),
            $diff->getAppliedCheckers(),
        ));

        $message = "Chunk has fixable errors: {$checkersAsClasses}";
        $lineStart = $chunk->start();
        $lineEnd = $lineStart + $chunk->startRange() - 1;

        return [
            'type' => 'issue',
            'description' => $message,
            'check_name' => $checkersAsFqcns,
            'fingerprint' => $this->generateFingerprint(
                $checkersAsFqcns,
                $message,
                $diff->getRelativeFilePath(),
                implode('\n', map(fn (Line $line) => "{$line->type()}:{$line->content()}", $chunk->lines())),
            ),
            'severity' => 'blocker',
            'categories' => ['Style'],
            'remediation_points' => 50_000,
            'location' => [
                'path' => $diff->getRelativeFilePath(),
                'lines' => [
                    'begin' => $lineStart,
                    'end' => $lineEnd,
                ],
            ],
        ];
    }

    /**
     * Generate a fingerprint for a given quality issue. This is used to
     * track the presence of an issue between runs, so it should be unique
     * and consistent for a given issue.
     *
     * Subsequently, changing the fingerprint or the data it uses is
     * _technically_ a breaking change. Users would see existing issues being
     * marked as "new" on the commit proceeding updating ECS.
     *
     * DO NOT include position information as salting, or every time
     * lines are added/removed the lines below it will be reported as
     * new errors.
     */
    private function generateFingerprint(
        string $checker,
        string $message,
        string $relativeFilePath,
        string $salt = '',
    ): string {
        // We implode to add a separator that cannot show up in PHP
        // class names or Linux file names and SHOULD  never show up in
        // messages. This guarantees the same fingerprint won't be generated
        // by accident, by the associative property of concatenation. As in:
        //
        // (ABC + ABC = ABCABC) == (ABCA + BC = ABCABC)
        // (ABC + \0 + ABC = ABC\0ABC) != (ABCA + \0 + BC = ABCA\0BC)
        return md5(implode("\0", [$checker, $message, $relativeFilePath, $salt]));
    }

    /**
     * @param GitlabIssue[] $lineItems
     */
    private function encode(array $lineItems): string
    {
        return json_encode(
            $lineItems,
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}
