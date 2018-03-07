<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Nette\Utils\Strings;
use Symfony\Component\Yaml\Yaml;

final class FileHashComputer
{
    public function compute(string $filePath): string
    {
        if (Strings::endsWith($filePath, '.yml')) {
            $loadedFileStructure = Yaml::parse(file_get_contents($filePath));

            return md5(serialize($loadedFileStructure));
        }

        return md5_file($filePath);
    }
}
