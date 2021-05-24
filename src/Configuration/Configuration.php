<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Configuration;

use ECSPrefix20210524\Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Exception\Configuration\SourceNotFoundException;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20210524\Symplify\PackageBuilder\Parameter\ParameterProvider;
final class Configuration
{
    /**
     * @var bool
     */
    private $isFixer = \false;
    /**
     * @var bool
     */
    private $shouldClearCache = \false;
    /**
     * @var bool
     */
    private $showProgressBar = \true;
    /**
     * @var bool
     */
    private $showErrorTable = \true;
    /**
     * @var string[]
     */
    private $sources = [];
    /**
     * @var string[]
     */
    private $paths = [];
    /**
     * @var string
     */
    private $outputFormat = \Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter::NAME;
    /**
     * @var bool
     */
    private $doesMatchGitDiff = \false;
    public function __construct(\ECSPrefix20210524\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider)
    {
        $this->paths = $parameterProvider->provideArrayParameter(\Symplify\EasyCodingStandard\ValueObject\Option::PATHS);
    }
    /**
     * Needs to run in the start of the life cycle, since the rest of workflow uses it.
     * @return void
     */
    public function resolveFromInput(\ECSPrefix20210524\Symfony\Component\Console\Input\InputInterface $input)
    {
        /** @var string[] $paths */
        $paths = (array) $input->getArgument(\Symplify\EasyCodingStandard\ValueObject\Option::PATHS);
        if ($paths !== []) {
            $this->setSources($paths);
        } else {
            // if not paths are provided from CLI, use the config ones
            $this->setSources($this->paths);
        }
        $this->isFixer = (bool) $input->getOption(\Symplify\EasyCodingStandard\ValueObject\Option::FIX);
        $this->shouldClearCache = (bool) $input->getOption(\Symplify\EasyCodingStandard\ValueObject\Option::CLEAR_CACHE);
        $this->showProgressBar = $this->canShowProgressBar($input);
        $this->showErrorTable = !(bool) $input->getOption(\Symplify\EasyCodingStandard\ValueObject\Option::NO_ERROR_TABLE);
        $this->doesMatchGitDiff = (bool) $input->getOption(\Symplify\EasyCodingStandard\ValueObject\Option::MATCH_GIT_DIFF);
        $this->setOutputFormat($input);
    }
    /**
     * @return string[]
     */
    public function getSources() : array
    {
        return $this->sources;
    }
    public function isFixer() : bool
    {
        return $this->isFixer;
    }
    public function shouldClearCache() : bool
    {
        return $this->shouldClearCache;
    }
    public function shouldShowProgressBar() : bool
    {
        return $this->showProgressBar;
    }
    public function shouldShowErrorTable() : bool
    {
        return $this->showErrorTable;
    }
    /**
     * @param string[] $sources
     * @return void
     */
    public function setSources(array $sources)
    {
        $this->ensureSourcesExists($sources);
        $this->sources = $this->normalizeSources($sources);
    }
    /**
     * @return string[]
     */
    public function getPaths() : array
    {
        return $this->paths;
    }
    public function getOutputFormat() : string
    {
        return $this->outputFormat;
    }
    /**
     * @api
     * For tests
     * @return void
     */
    public function enableFixing()
    {
        $this->isFixer = \true;
    }
    public function doesMatchGitDiff() : bool
    {
        return $this->doesMatchGitDiff;
    }
    private function canShowProgressBar(\ECSPrefix20210524\Symfony\Component\Console\Input\InputInterface $input) : bool
    {
        $notJsonOutput = $input->getOption(\Symplify\EasyCodingStandard\ValueObject\Option::OUTPUT_FORMAT) !== \Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter::NAME;
        if (!$notJsonOutput) {
            return \false;
        }
        return !(bool) $input->getOption(\Symplify\EasyCodingStandard\ValueObject\Option::NO_PROGRESS_BAR);
    }
    /**
     * @param string[] $sources
     * @return void
     */
    private function ensureSourcesExists(array $sources)
    {
        foreach ($sources as $source) {
            if (\file_exists($source)) {
                continue;
            }
            throw new \Symplify\EasyCodingStandard\Exception\Configuration\SourceNotFoundException(\sprintf('Source "%s" does not exist.', $source));
        }
    }
    /**
     * @param string[] $sources
     * @return string[]
     */
    private function normalizeSources(array $sources) : array
    {
        foreach ($sources as $key => $value) {
            $sources[$key] = \rtrim($value, \DIRECTORY_SEPARATOR);
        }
        return $sources;
    }
    /**
     * @return void
     */
    private function setOutputFormat(\ECSPrefix20210524\Symfony\Component\Console\Input\InputInterface $input)
    {
        $outputFormat = (string) $input->getOption(\Symplify\EasyCodingStandard\ValueObject\Option::OUTPUT_FORMAT);
        // Backwards compatibility with older version
        if ($outputFormat === 'table') {
            $this->outputFormat = \Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter::NAME;
        }
        $this->outputFormat = $outputFormat;
    }
}
