<?php

namespace Symplify\SetConfigResolver;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\SetConfigResolver\Console\Option\OptionName;
use Symplify\SetConfigResolver\Console\OptionValueResolver;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
use Symplify\SmartFileSystem\SmartFileInfo;

abstract class AbstractConfigResolver
{
    /**
     * @var OptionValueResolver
     */
    private $optionValueResolver;

    public function __construct()
    {
        $this->optionValueResolver = new OptionValueResolver();
    }

    /**
     * @return \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    public function resolveFromInput(InputInterface $input)
    {
        $configValue = $this->optionValueResolver->getOptionValue($input, OptionName::CONFIG);

        if ($configValue !== null) {
            if (! file_exists($configValue)) {
                $message = sprintf('File "%s" was not found', $configValue);
                throw new FileNotFoundException($message);
            }

            return $this->createFileInfo($configValue);
        }

        return null;
    }

    /**
     * @param string[] $fallbackFiles
     * @return \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    public function resolveFromInputWithFallback(InputInterface $input, array $fallbackFiles)
    {
        $configFileInfo = $this->resolveFromInput($input);
        if ($configFileInfo !== null) {
            return $configFileInfo;
        }

        return $this->createFallbackFileInfoIfFound($fallbackFiles);
    }

    /**
     * @param string[] $fallbackFiles
     * @return \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    private function createFallbackFileInfoIfFound(array $fallbackFiles)
    {
        foreach ($fallbackFiles as $fallbackFile) {
            $rootFallbackFile = getcwd() . DIRECTORY_SEPARATOR . $fallbackFile;
            if (is_file($rootFallbackFile)) {
                return $this->createFileInfo($rootFallbackFile);
            }
        }

        return null;
    }

    /**
     * @param string $configValue
     * @return \Symplify\SmartFileSystem\SmartFileInfo
     */
    private function createFileInfo($configValue)
    {
        $configValue = (string) $configValue;
        return new SmartFileInfo($configValue);
    }
}
