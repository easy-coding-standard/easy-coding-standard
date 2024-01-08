<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

use Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\Exception\Configuration\SuperfluousConfigurationException;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

/**
 * @api
 */
final class ECSConfigBuilder
{
    /**
     * @var string[]
     */
    private array $paths = [];

    /**
     * @var string[]
     */
    private array $sets = [];

    /**
     * @var array<mixed>
     */
    private array $skip = [];

    public function __invoke(ECSConfig $ecsConfig): void
    {
        $ecsConfig->sets($this->sets);
        $ecsConfig->paths($this->paths);
        $ecsConfig->skip($this->skip);
    }

    /**
     * @param string[] $paths
     */
    public function withPaths(array $paths): self
    {
        $this->paths = $paths;

        return $this;
    }

    /**
     * @param array<mixed> $skip
     */
    public function withSkip(array $skip): self
    {
        $this->skip = $skip;

        return $this;
    }

    /**
     * Include PHP files from the root directory,
     * typically ecs.php, rector.php etc.
     */
    public function withRootFiles(): self
    {
        $rootPhpFilesFinder = (new Finder())->files()
            ->in(getcwd())
            ->depth(0)
            ->name('*.php');

        foreach ($rootPhpFilesFinder as $rootPhpFileFinder) {
            $this->paths[] = $rootPhpFileFinder->getRealPath();
        }

        return $this;
    }

    public function withPreparedSets(
        /** @see SetList::PSR_12 */
        bool $psr12 = false,
        /** @see SetList::COMMON */
        bool $common = false,
        /** @see SetList::SYMPLIFY */
        bool $symplify = false,
        // common sets
        /** @see SetList::ARRAY */
        bool $arrays = false,
        /** @see SetList::COMMENTS */
        bool $comments = false,
        /** @see SetList::DOCBLOCK */
        bool $docblocks = false,
        /** @see SetList::SPACES */
        bool $spaces = false,
        /** @see SetList::NAMESPACES */
        bool $namespaces = false,
        /** @see SetList::CONTROL_STRUCTURES */
        bool $controlStructures = false,
        /** @see SetList::PHPUNIT */
        bool $phpunit = false,
        /** @see SetList::STRICT */
        bool $strict = false,
    ): self {
        if ($psr12) {
            $this->sets[] = SetList::PSR_12;
        }

        if ($common) {
            // include all "common" sets
            $this->sets[] = SetList::COMMON;

            if ($arrays || $spaces || $namespaces || $docblocks || $controlStructures || $phpunit || $strict || $comments) {
                throw new SuperfluousConfigurationException('This set is already included in the "common" set. You can remove it');
            }
        } else {
            if ($arrays) {
                $this->sets[] = SetList::ARRAY;
            }

            if ($spaces) {
                $this->sets[] = SetList::SPACES;
            }

            if ($namespaces) {
                $this->sets[] = SetList::NAMESPACES;
            }

            if ($docblocks) {
                $this->sets[] = SetList::DOCBLOCK;
            }

            if ($controlStructures) {
                $this->sets[] = SetList::CONTROL_STRUCTURES;
            }

            if ($phpunit) {
                $this->sets[] = SetList::PHPUNIT;
            }

            if ($strict) {
                $this->sets[] = SetList::STRICT;
            }

            if ($comments) {
                $this->sets[] = SetList::COMMENTS;
            }
        }

        if ($symplify) {
            $this->sets[] = SetList::SYMPLIFY;
        }

        return $this;
    }

    /**
     * @param string[] $sets
     */
    public function withSets(array $sets): self
    {
        $this->sets = [...$this->sets, ...$sets];

        return $this;
    }
}
