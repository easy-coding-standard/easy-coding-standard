<?php

namespace ECSPrefix20210517\Symplify\SetConfigResolver;

use ECSPrefix20210517\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210517\Symplify\SetConfigResolver\Console\Option\OptionName;
use ECSPrefix20210517\Symplify\SetConfigResolver\Console\OptionValueResolver;
use ECSPrefix20210517\Symplify\SmartFileSystem\Exception\FileNotFoundException;
use ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo;
abstract class AbstractConfigResolver
{
    /**
     * @var OptionValueResolver
     */
    private $optionValueResolver;
    public function __construct()
    {
        $this->optionValueResolver = new \ECSPrefix20210517\Symplify\SetConfigResolver\Console\OptionValueResolver();
    }
    /**
     * @return \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    public function resolveFromInput(\ECSPrefix20210517\Symfony\Component\Console\Input\InputInterface $input)
    {
        $configValue = $this->optionValueResolver->getOptionValue($input, \ECSPrefix20210517\Symplify\SetConfigResolver\Console\Option\OptionName::CONFIG);
        if ($configValue !== null) {
            if (!\file_exists($configValue)) {
                $message = \sprintf('File "%s" was not found', $configValue);
                throw new \ECSPrefix20210517\Symplify\SmartFileSystem\Exception\FileNotFoundException($message);
            }
            return $this->createFileInfo($configValue);
        }
        return null;
    }
    /**
     * @param string[] $fallbackFiles
     * @return \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    public function resolveFromInputWithFallback(\ECSPrefix20210517\Symfony\Component\Console\Input\InputInterface $input, array $fallbackFiles)
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
            $rootFallbackFile = \getcwd() . \DIRECTORY_SEPARATOR . $fallbackFile;
            if (\is_file($rootFallbackFile)) {
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
        return new \ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo($configValue);
    }
}
