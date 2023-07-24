<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching;

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\DependencyInjection\SimpleParameterProvider;
use Symplify\EasyCodingStandard\Exception\Configuration\FileNotFoundException;
use Webmozart\Assert\Assert;

/**
 * @see \Symplify\EasyCodingStandard\Tests\ChangedFilesDetector\FileHashComputer\FileHashComputerTest
 */
final class FileHashComputer
{
    public function computeConfig(string $filePath): string
    {

        $callable = require $filePath;
        Assert::isCallable($callable);

        $ecsConfig = new ECSConfig();
        $callable($ecsConfig);
        $fileHash = sha1(json_encode($ecsConfig->getBindings()));

        return $fileHash . SimpleParameterProvider::hash();
    }

    public function compute(string $filePath): string
    {
        $fileHash = md5_file($filePath);
        if (! $fileHash) {
            throw new FileNotFoundException(sprintf('File "%s" was not found', $fileHash));
        }

        return $fileHash;
    }
}
