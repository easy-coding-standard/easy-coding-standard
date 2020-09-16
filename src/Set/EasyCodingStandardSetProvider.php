<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Set;

use Nette\Utils\Strings;
use ReflectionClass;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\SetConfigResolver\Provider\AbstractSetProvider;
use Symplify\SetConfigResolver\ValueObject\Set;
use Symplify\SmartFileSystem\SmartFileInfo;

final class EasyCodingStandardSetProvider extends AbstractSetProvider
{
    /**
     * @see https://regex101.com/r/mkleqU/1
     * @var string
     */
    private const REMOVE_DASH_BEFORE_NUMBER_REGEX = '#([a-z])-(\d+)$$#';

    /**
     * @var string
     */
    private const UNDERSCORE_PATTERN = '#_#';

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

            // back compatible names without "-"
            $setName = Strings::replace($setName, self::REMOVE_DASH_BEFORE_NUMBER_REGEX, '$1$2');
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
        return Strings::replace($string, self::UNDERSCORE_PATTERN, '-');
    }
}
