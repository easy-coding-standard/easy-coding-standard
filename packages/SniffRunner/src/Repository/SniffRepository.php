<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Repository;

use Symplify\EasyCodingStandard\SniffRunner\Naming\SniffGroupNameResolver;
use Symplify\EasyCodingStandard\SniffRunner\Sniff\Finder\SniffFinder;

final class SniffRepository
{
    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    /**
     * @var string[][]
     */
    private $sniffClasses;

    /**
     * @var SniffGroupNameResolver
     */
    private $sniffGroupNameResolver;

    public function __construct(SniffFinder $sniffFinder, SniffGroupNameResolver $sniffGroupNameResolver)
    {
        $this->sniffFinder = $sniffFinder;
        $this->sniffGroupNameResolver = $sniffGroupNameResolver;
    }

    /**
     * @return string[]
     */
    public function getGroups(): array
    {
        $this->init();

        $groups = array_keys($this->sniffClasses);
        return array_combine($groups, $groups);
    }

    /**
     * @return string[]
     */
    public function getByGroup(string $group): array
    {
        $this->init();

        if (!isset($this->sniffClasses[$group])) {
            return [];
        }

        return $this->sniffClasses[$group];
    }

    private function init(): void
    {
        if (count($this->sniffClasses)) {
            return;
        }

        $sniffClasses = $this->sniffFinder->findAllSniffClasses();

        foreach ($sniffClasses as $sniffClass) {
            $group = $this->sniffGroupNameResolver->resolveFromSniffClass($sniffClass);
            $this->sniffClasses[$group][] = $sniffClass;
        }
    }
}
