<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Compiler\Packagist;

use Nette\Utils\Json;
use PharIo\Version\Version;
use Symplify\SmartFileSystem\SmartFileSystem;

final class SymplifyStableVersionProvider
{
    /**
     * @var string|null
     */
    private $symplifyVersionToRequire;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(SmartFileSystem $smartFileSystem)
    {
        $this->smartFileSystem = $smartFileSystem;
    }

    public function provide(): string
    {
        if ($this->symplifyVersionToRequire !== null) {
            return $this->symplifyVersionToRequire;
        }

        $symplifyPackageContent = $this->smartFileSystem->readFile(
            'https://repo.packagist.org/p/symplify/symplify.json'
        );

        $symplifyPackageJson = $this->loadContentJsonStringToArray($symplifyPackageContent);
        $symplifyPackageVersions = $symplifyPackageJson['packages']['symplify/symplify'];

        $lastStableVersion = $this->getLastKey($symplifyPackageVersions);

        $lastStableVersion = new Version($lastStableVersion);

        $this->symplifyVersionToRequire = '^' . $lastStableVersion->getMajor()->getValue() . '.' . $lastStableVersion->getMinor()->getValue();

        return $this->symplifyVersionToRequire;
    }

    private function loadContentJsonStringToArray(string $jsonContent): array
    {
        return Json::decode($jsonContent, Json::FORCE_ARRAY);
    }

    private function getLastKey(array $items): string
    {
        end($items);

        return key($items);
    }
}
