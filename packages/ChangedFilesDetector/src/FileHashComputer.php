<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector;

use Nette\DI\Config\Loader;
use Nette\Utils\Strings;

final class FileHashComputer
{
    /**
     * @var Loader
     */
    private $loader;

    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    public function compute(string $filePath): string
    {
        if (Strings::endsWith($filePath, '.neon')) {
            $loadedFileStructure = $this->loader->load($filePath);

            return md5(serialize($loadedFileStructure));
        }

        return md5_file($filePath);
    }
}
