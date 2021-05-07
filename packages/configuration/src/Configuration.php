<?php

namespace Symplify\EasyCodingStandard\Configuration;

use ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Exception\Configuration\SourceNotFoundException;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
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
    private $outputFormat = ConsoleOutputFormatter::NAME;
    /**
     * @var bool
     */
    private $doesMatchGitDiff = \false;
    /**
     * @param \Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider
     */
    public function __construct($parameterProvider)
    {
        $this->paths = $parameterProvider->provideArrayParameter(Option::PATHS);
    }
    /**
     * Needs to run in the start of the life cycle, since the rest of workflow uses it.
     * @return void
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     */
    public function resolveFromInput($input)
    {
        /** @var string[] $paths */
        $paths = (array) $input->getArgument(Option::PATHS);
        if ($paths !== []) {
            $this->setSources($paths);
        } else {
            // if not paths are provided from CLI, use the config ones
            $this->setSources($this->paths);
        }
        $this->isFixer = (bool) $input->getOption(Option::FIX);
        $this->shouldClearCache = (bool) $input->getOption(Option::CLEAR_CACHE);
        $this->showProgressBar = $this->canShowProgressBar($input);
        $this->showErrorTable = !(bool) $input->getOption(Option::NO_ERROR_TABLE);
        $this->doesMatchGitDiff = (bool) $input->getOption(Option::MATCH_GIT_DIFF);
        $this->setOutputFormat($input);
    }
    /**
     * @return mixed[]
     */
    public function getSources()
    {
        return $this->sources;
    }
    /**
     * @return bool
     */
    public function isFixer()
    {
        return $this->isFixer;
    }
    /**
     * @return bool
     */
    public function shouldClearCache()
    {
        return $this->shouldClearCache;
    }
    /**
     * @return bool
     */
    public function shouldShowProgressBar()
    {
        return $this->showProgressBar;
    }
    /**
     * @return bool
     */
    public function shouldShowErrorTable()
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
     * @return mixed[]
     */
    public function getPaths()
    {
        return $this->paths;
    }
    /**
     * @return string
     */
    public function getOutputFormat()
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
    /**
     * @return bool
     */
    public function doesMatchGitDiff()
    {
        return $this->doesMatchGitDiff;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     * @return bool
     */
    private function canShowProgressBar($input)
    {
        $notJsonOutput = $input->getOption(Option::OUTPUT_FORMAT) !== JsonOutputFormatter::NAME;
        if (!$notJsonOutput) {
            return \false;
        }
        return !(bool) $input->getOption(Option::NO_PROGRESS_BAR);
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
            throw new SourceNotFoundException(\sprintf('Source "%s" does not exist.', $source));
        }
    }
    /**
     * @param string[] $sources
     * @return mixed[]
     */
    private function normalizeSources(array $sources)
    {
        foreach ($sources as $key => $value) {
            $sources[$key] = \rtrim($value, \DIRECTORY_SEPARATOR);
        }
        return $sources;
    }
    /**
     * @return void
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     */
    private function setOutputFormat($input)
    {
        $outputFormat = (string) $input->getOption(Option::OUTPUT_FORMAT);
        // Backwards compatibility with older version
        if ($outputFormat === 'table') {
            $this->outputFormat = ConsoleOutputFormatter::NAME;
        }
        $this->outputFormat = $outputFormat;
    }
}
