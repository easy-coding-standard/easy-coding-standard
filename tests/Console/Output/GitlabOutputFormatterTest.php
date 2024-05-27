<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Output;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PhpCsFixer\Differ\DifferInterface;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Console\Formatter\ColorConsoleDiffFormatter;
use Symplify\EasyCodingStandard\Console\Output\GitlabOutputFormatter;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractTestCase;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;

final class GitlabOutputFormatterTest extends AbstractTestCase
{
    private GitlabOutputFormatter $gitlabOutputFormatter;

    private ColorConsoleDiffFormatter $colorConsoleDiffFormatter;

    private DifferInterface $differ;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gitlabOutputFormatter = $this->make(gitlabOutputFormatter::class);
        $this->colorConsoleDiffFormatter = $this->make(ColorConsoleDiffFormatter::class);
        $this->differ = $this->make(DifferInterface::class);
    }

    /**
     * @return array{CodingStandardError[], FileDiff[]}
     */
    public function getMockedIssues(string $filePathForChanges = null): array
    {
        $filePathForOriginal = __DIR__ . '/Source/RandomFile.php';

        $simulatedErrors = [
            new CodingStandardError(3, 'This is a test', LineLengthSniff::class, $filePathForOriginal),
            new CodingStandardError(5, 'This is another test', LineLengthSniff::class, $filePathForOriginal),
        ];

        if ($filePathForChanges === null) {
            return [$simulatedErrors, []];
        }

        $diff = $this->differ->diff(
            file_get_contents($filePathForOriginal) ?: 'ERROR 1',
            file_get_contents($filePathForChanges) ?: 'ERROR 2',
        );

        $simulatedFixes = [
            new FileDiff(
                $filePathForOriginal,
                $diff,
                $this->colorConsoleDiffFormatter->format($diff),
                [LineLengthFixer::class],
            ),
        ];

        return [$simulatedErrors, $simulatedFixes];
    }

    public function testGracefullyHandlesNoIssues(): void
    {
        $configuration = new Configuration();
        $filePathForExpectedOutput = __DIR__ . '/Fixture/gitlab/no_issues.json';

        $errorAndDiffResult = new ErrorAndDiffResult([], [], []);

        $this->assertJsonStringEqualsJsonFile(
            $filePathForExpectedOutput,
            $this->gitlabOutputFormatter->generateReport($errorAndDiffResult, $configuration),
        );
    }

    public function testReportsErrorsInTheRightFormat(): void
    {
        $configuration = new Configuration();
        $filePathForExpectedOutput = __DIR__ . '/Fixture/gitlab/only_errors.json';

        [$simulatedErrors] = $this->getMockedIssues();

        $errorAndDiffResult = new ErrorAndDiffResult($simulatedErrors, [], []);
        $output = $this->gitlabOutputFormatter->generateReport($errorAndDiffResult, $configuration);

        $this->assertJsonStringEqualsJsonFile($filePathForExpectedOutput, $output);
    }

    public function testReportsFixesInTheRightFormat(): void
    {
        $configuration = new Configuration();
        $filePathForChanges = __DIR__ . '/Source/RandomFileWithEdits.php';
        $filePathForExpectedOutput = __DIR__ . '/Fixture/gitlab/only_fixes.json';

        [$_, $simulatedFixes] = $this->getMockedIssues($filePathForChanges);

        $errorAndDiffResult = new ErrorAndDiffResult([], $simulatedFixes, []);
        $output = $this->gitlabOutputFormatter->generateReport($errorAndDiffResult, $configuration);

        $this->assertJsonStringEqualsJsonFile($filePathForExpectedOutput, $output);
    }

    public function testReportsErrorsAndFixesByDefault(): void
    {
        $configuration = new Configuration();
        $filePathForChanges = __DIR__ . '/Source/RandomFileWithEdits.php';
        $filePathForExpectedOutput = __DIR__ . '/Fixture/gitlab/errors_and_fixes.json';

        [$simulatedErrors, $simulatedFixes] = $this->getMockedIssues($filePathForChanges);

        $errorAndDiffResult = new ErrorAndDiffResult($simulatedErrors, $simulatedFixes, []);
        $output = $this->gitlabOutputFormatter->generateReport($errorAndDiffResult, $configuration);

        $this->assertJsonStringEqualsJsonFile($filePathForExpectedOutput, $output);
    }

    public function testReportsOnlyErrorsWithNoDiffsFlag(): void
    {
        $configuration = new Configuration(showDiffs: false);
        $filePathForChanges = __DIR__ . '/Source/RandomFileWithEdits.php';
        $filePathForExpectedOutput = __DIR__ . '/Fixture/gitlab/only_errors.json';

        [$simulatedErrors, $simulatedFixes] = $this->getMockedIssues($filePathForChanges);

        $errorAndDiffResult = new ErrorAndDiffResult($simulatedErrors, $simulatedFixes, []);
        $output = $this->gitlabOutputFormatter->generateReport($errorAndDiffResult, $configuration);

        $this->assertJsonStringEqualsJsonFile($filePathForExpectedOutput, $output);
    }

    public function testReportsOnlyErrorsWithFixFlag(): void
    {
        $configuration = new Configuration(isFixer: true);
        $filePathForChanges = __DIR__ . '/Source/RandomFileWithEdits.php';
        $filePathForExpectedOutput = __DIR__ . '/Fixture/gitlab/only_errors.json';

        [$simulatedErrors, $simulatedFixes] = $this->getMockedIssues($filePathForChanges);

        $errorAndDiffResult = new ErrorAndDiffResult($simulatedErrors, $simulatedFixes, []);
        $output = $this->gitlabOutputFormatter->generateReport($errorAndDiffResult, $configuration);

        $this->assertJsonStringEqualsJsonFile($filePathForExpectedOutput, $output);
    }

    // #[Depends('testReportsFixesInTheRightFormat')]
    public function testIssueFingerpintsDoNotChangeFromSimpleLineOffsets(): void
    {
        $configuration = new Configuration();
        $filePathForOriginal = __DIR__ . '/Source/RandomFileWithSimpleOffset.php';
        $mockedFilePathForOriginal = __DIR__ . '/Source/RandomFile.php';
        $filePathForChanges = __DIR__ . '/Source/RandomFileWithEditsAndSimpleOffset.php';
        $filePathForExpectedOutput = __DIR__ . '/Fixture/gitlab/only_fixes_with_offset.json';

        $diff = $this->differ->diff(
            file_get_contents($filePathForOriginal) ?: 'ERROR 1',
            file_get_contents($filePathForChanges) ?: 'ERROR 2',
        );

        $simulatedFixes = [
            new FileDiff(
                $mockedFilePathForOriginal,
                $diff,
                $this->colorConsoleDiffFormatter->format($diff),
                [LineLengthFixer::class],
            ),
        ];

        $errorAndDiffResult = new ErrorAndDiffResult([], $simulatedFixes, []);
        $output = $this->gitlabOutputFormatter->generateReport($errorAndDiffResult, $configuration);

        $this->assertJsonStringEqualsJsonFile($filePathForExpectedOutput, $output);
    }
}
