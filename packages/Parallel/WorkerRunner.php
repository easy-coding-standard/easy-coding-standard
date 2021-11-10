<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel;

use ECSPrefix20211110\Clue\React\NDJson\Decoder;
use ECSPrefix20211110\Clue\React\NDJson\Encoder;
use Symplify\EasyCodingStandard\Application\SingleFileProcessor;
use Symplify\EasyCodingStandard\Parallel\Enum\Action;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;
use ECSPrefix20211110\Symplify\PackageBuilder\Yaml\ParametersMerger;
use ECSPrefix20211110\Symplify\SmartFileSystem\SmartFileInfo;
use Throwable;
final class WorkerRunner
{
    /**
     * @var string
     */
    private const RESULT = 'result';
    /**
     * @var \Symplify\EasyCodingStandard\Application\SingleFileProcessor
     */
    private $singleFileProcessor;
    /**
     * @var \Symplify\PackageBuilder\Yaml\ParametersMerger
     */
    private $parametersMerger;
    public function __construct(\Symplify\EasyCodingStandard\Application\SingleFileProcessor $singleFileProcessor, \ECSPrefix20211110\Symplify\PackageBuilder\Yaml\ParametersMerger $parametersMerger)
    {
        $this->singleFileProcessor = $singleFileProcessor;
        $this->parametersMerger = $parametersMerger;
    }
    public function run(\ECSPrefix20211110\Clue\React\NDJson\Encoder $encoder, \ECSPrefix20211110\Clue\React\NDJson\Decoder $decoder, \Symplify\EasyCodingStandard\ValueObject\Configuration $configuration) : void
    {
        // 1. handle system error
        $handleErrorCallback = static function (\Throwable $throwable) use($encoder) : void {
            $systemErrors = new \Symplify\EasyCodingStandard\ValueObject\Error\SystemError($throwable->getLine(), $throwable->getMessage(), $throwable->getFile());
            $encoder->write([\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::ACTION => self::RESULT, self::RESULT => [\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS => [$systemErrors], \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILES_COUNT => 0, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS_COUNT => 1]]);
            $encoder->end();
        };
        $encoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::ERROR, $handleErrorCallback);
        // 2. collect diffs + errors from file processor
        $decoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::DATA, function (array $json) use($encoder, $configuration) : void {
            $action = $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::ACTION];
            if ($action !== \Symplify\EasyCodingStandard\Parallel\Enum\Action::CHECK) {
                return;
            }
            $systemErrorsCount = 0;
            /** @var string[] $filePaths */
            $filePaths = $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILES] ?? [];
            $errorAndFileDiffs = [];
            $systemErrors = [];
            foreach ($filePaths as $filePath) {
                try {
                    $smartFileInfo = new \ECSPrefix20211110\Symplify\SmartFileSystem\SmartFileInfo($filePath);
                    $currentErrorsAndFileDiffs = $this->singleFileProcessor->processFileInfo($smartFileInfo, $configuration);
                    $errorAndFileDiffs = $this->parametersMerger->merge($errorAndFileDiffs, $currentErrorsAndFileDiffs);
                } catch (\Throwable $throwable) {
                    ++$systemErrorsCount;
                    $errorMessage = \sprintf('System error: "%s"', $throwable->getMessage());
                    $errorMessage .= 'Run ECS with "--debug" option and post the report here: https://github.com/symplify/symplify/issues/new';
                    $systemErrors[] = new \Symplify\EasyCodingStandard\ValueObject\Error\SystemError($throwable->getLine(), $errorMessage, $filePath);
                }
            }
            /**
             * this invokes all listeners listening $decoder->on(...) @see ReactEvent::DATA
             */
            $encoder->write([\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::ACTION => self::RESULT, self::RESULT => [\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::CODING_STANDARD_ERRORS => $errorAndFileDiffs[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::CODING_STANDARD_ERRORS] ?? [], \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILE_DIFFS => $errorAndFileDiffs[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILE_DIFFS] ?? [], \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::FILES_COUNT => \count($filePaths), \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS => $systemErrors, \Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::SYSTEM_ERRORS_COUNT => $systemErrorsCount]]);
        });
        $decoder->on(\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactEvent::ERROR, $handleErrorCallback);
    }
}
