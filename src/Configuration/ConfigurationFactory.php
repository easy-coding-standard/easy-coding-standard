<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Configuration;

use ECSPrefix20210619\Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Exception\Configuration\SourceNotFoundException;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20210619\Symplify\PackageBuilder\Parameter\ParameterProvider;
final class ConfigurationFactory
{
    /**
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    public function __construct(\ECSPrefix20210619\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }
    /**
     * Needs to run in the start of the life cycle, since the rest of workflow uses it.
     */
    public function createFromInput(\ECSPrefix20210619\Symfony\Component\Console\Input\InputInterface $input) : \Symplify\EasyCodingStandard\ValueObject\Configuration
    {
        $sources = $this->resolvePaths($input);
        $isFixer = (bool) $input->getOption(\Symplify\EasyCodingStandard\ValueObject\Option::FIX);
        $shouldClearCache = (bool) $input->getOption(\Symplify\EasyCodingStandard\ValueObject\Option::CLEAR_CACHE);
        $showProgressBar = $this->canShowProgressBar($input);
        $showErrorTable = !(bool) $input->getOption(\Symplify\EasyCodingStandard\ValueObject\Option::NO_ERROR_TABLE);
        $doesMatchGitDiff = (bool) $input->getOption(\Symplify\EasyCodingStandard\ValueObject\Option::MATCH_GIT_DIFF);
        $outputFormat = (string) $input->getOption(\Symplify\EasyCodingStandard\ValueObject\Option::OUTPUT_FORMAT);
        return new \Symplify\EasyCodingStandard\ValueObject\Configuration($isFixer, $shouldClearCache, $showProgressBar, $showErrorTable, $sources, $outputFormat, $doesMatchGitDiff);
    }
    private function canShowProgressBar(\ECSPrefix20210619\Symfony\Component\Console\Input\InputInterface $input) : bool
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
     * @return string[]
     */
    private function resolvePaths(\ECSPrefix20210619\Symfony\Component\Console\Input\InputInterface $input) : array
    {
        /** @var string[] $paths */
        $paths = (array) $input->getArgument(\Symplify\EasyCodingStandard\ValueObject\Option::PATHS);
        if ($paths !== []) {
            $sources = $paths;
        } else {
            // if not paths are provided from CLI, use the config ones
            $sources = $this->parameterProvider->provideArrayParameter(\Symplify\EasyCodingStandard\ValueObject\Option::PATHS);
        }
        $this->ensureSourcesExists($sources);
        return $this->normalizeSources($sources);
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
}
