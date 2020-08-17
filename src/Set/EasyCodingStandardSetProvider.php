<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Set;

use Nette\Utils\Strings;
use ReflectionClass;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\SetConfigResolver\Contract\SetProviderInterface;
use Symplify\SetConfigResolver\Provider\AbstractSetProvider;
use Symplify\SetConfigResolver\ValueObject\Set;
use Symplify\SmartFileSystem\SmartFileInfo;

final class EasyCodingStandardSetProvider extends AbstractSetProvider implements SetProviderInterface
{
    /**
     * @var Set[]
     */
    private $sets = [];

    public function __construct()
    {
        $setListReflectionClass = new ReflectionClass(SetList::class);

        // new kind of paths sets
        foreach ($setListReflectionClass->getConstants() as $name => $setPath) {
            if (! file_exists($setPath)) {
                continue;
            }

            $setName = $this->constantToDashes($name);
            $this->sets[] = new Set($setName, new SmartFileInfo($setPath));
        }
    }

    /**
     * @return Set[]
     */
    public function provide(): array
    {
        return $this->sets;
    }

    /**
     * @return string[]
     */
    public function provideSetNames(): array
    {
        $setNames = [];
        foreach ($this->sets as $set) {
            $setNames[] = $set->getName();
        }

        sort($setNames);

        return $setNames;
    }

    public function constantToDashes(string $string): string
    {
        $string = strtolower($string);
        return Strings::replace($string, '#_#', '-');
    }
}
