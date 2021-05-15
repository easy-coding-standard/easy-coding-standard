<?php

namespace ECSPrefix20210515\Symplify\SetConfigResolver\Provider;

use ECSPrefix20210515\Nette\Utils\Strings;
use ECSPrefix20210515\Symplify\SetConfigResolver\Contract\SetProviderInterface;
use ECSPrefix20210515\Symplify\SetConfigResolver\Exception\SetNotFoundException;
use ECSPrefix20210515\Symplify\SetConfigResolver\ValueObject\Set;
use ECSPrefix20210515\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
abstract class AbstractSetProvider implements \ECSPrefix20210515\Symplify\SetConfigResolver\Contract\SetProviderInterface
{
    /**
     * @return mixed[]
     */
    public function provideSetNames()
    {
        $setNames = [];
        $sets = $this->provide();
        foreach ($sets as $set) {
            $setNames[] = $set->getName();
        }
        return $setNames;
    }
    /**
     * @return \Symplify\SetConfigResolver\ValueObject\Set|null
     * @param string $desiredSetName
     */
    public function provideByName($desiredSetName)
    {
        $desiredSetName = (string) $desiredSetName;
        // 1. name-based approach
        $sets = $this->provide();
        foreach ($sets as $set) {
            if ($set->getName() !== $desiredSetName) {
                continue;
            }
            return $set;
        }
        // 2. path-based approach
        try {
            $sets = $this->provide();
            foreach ($sets as $set) {
                // possible bug for PHAR files, see https://bugs.php.net/bug.php?id=52769
                // this is very tricky to handle, see https://stackoverflow.com/questions/27838025/how-to-get-a-phar-file-real-directory-within-the-phar-file-code
                $setUniqueId = $this->resolveSetUniquePathId($set->getSetPathname());
                $desiredSetUniqueId = $this->resolveSetUniquePathId($desiredSetName);
                if ($setUniqueId !== $desiredSetUniqueId) {
                    continue;
                }
                return $set;
            }
        } catch (\ECSPrefix20210515\Symplify\SymplifyKernel\Exception\ShouldNotHappenException $shouldNotHappenException) {
        }
        $message = \sprintf('Set "%s" was not found', $desiredSetName);
        throw new \ECSPrefix20210515\Symplify\SetConfigResolver\Exception\SetNotFoundException($message, $desiredSetName, $this->provideSetNames());
    }
    /**
     * @param string $setPath
     * @return string
     */
    private function resolveSetUniquePathId($setPath)
    {
        $setPath = (string) $setPath;
        $setPath = \ECSPrefix20210515\Nette\Utils\Strings::after($setPath, \DIRECTORY_SEPARATOR, -2);
        if ($setPath === null) {
            throw new \ECSPrefix20210515\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $setPath;
    }
}
