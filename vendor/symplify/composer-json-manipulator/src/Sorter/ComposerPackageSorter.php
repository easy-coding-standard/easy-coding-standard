<?php

namespace Symplify\ComposerJsonManipulator\Sorter;

use ECSPrefix20210508\Nette\Utils\Strings;
/**
 * Mostly inspired by https://github.com/composer/composer/blob/master/src/Composer/Json/JsonManipulator.php
 *
 * @see \Symplify\ComposerJsonManipulator\Tests\Sorter\ComposerPackageSorterTest
 */
final class ComposerPackageSorter
{
    /**
     * @see https://regex101.com/r/tMrjMY/1
     * @var string
     */
    const PLATFORM_PACKAGE_REGEX = '#^(?:php(?:-64bit|-ipv6|-zts|-debug)?|hhvm|(?:ext|lib)-[a-z0-9](?:[_.-]?[a-z0-9]+)*|composer-(?:plugin|runtime)-api)$#iD';
    /**
     * @see https://regex101.com/r/SXZcfb/1
     * @var string
     */
    const REQUIREMENT_TYPE_REGEX = '#^(?<name>php|hhvm|ext|lib|\\D)#';
    /**
     * Sorts packages by importance (platform packages first, then PHP dependencies) and alphabetically.
     *
     * @link https://getcomposer.org/doc/02-libraries.md#platform-packages
     *
     * @param array<string, string> $packages
     * @return mixed[]
     */
    public function sortPackages(array $packages = [])
    {
        \uksort($packages, function (string $firstPackageName, string $secondPackageName) : int {
            $battleShipcompare = function ($left, $right) {
                if ($left === $right) {
                    return 0;
                }
                return $left < $right ? -1 : 1;
            };
            return $battleShipcompare($this->createNameWithPriority($firstPackageName), $this->createNameWithPriority($secondPackageName));
        });
        return $packages;
    }
    /**
     * @param string $requirementName
     * @return string
     */
    private function createNameWithPriority($requirementName)
    {
        if (\is_object($requirementName)) {
            $requirementName = (string) $requirementName;
        }
        if ($this->isPlatformPackage($requirementName)) {
            return \ECSPrefix20210508\Nette\Utils\Strings::replace($requirementName, self::REQUIREMENT_TYPE_REGEX, function (array $match) : string {
                $name = $match['name'];
                if ($name === 'php') {
                    return '0-' . $name;
                }
                if ($name === 'hhvm') {
                    return '0-' . $name;
                }
                if ($name === 'ext') {
                    return '1-' . $name;
                }
                if ($name === 'lib') {
                    return '2-' . $name;
                }
                return '3-' . $name;
            });
        }
        return '4-' . $requirementName;
    }
    /**
     * @param string $name
     * @return bool
     */
    private function isPlatformPackage($name)
    {
        if (\is_object($name)) {
            $name = (string) $name;
        }
        return (bool) \ECSPrefix20210508\Nette\Utils\Strings::match($name, self::PLATFORM_PACKAGE_REGEX);
    }
}
