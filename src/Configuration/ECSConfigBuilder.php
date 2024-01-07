<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Configuration;

use ECSPrefix202401\Symfony\Component\Finder\Finder;
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
    private $paths = [];
    /**
     * @var string[]
     */
    private $sets = [];
    public function __invoke(ECSConfig $ecsConfig) : void
    {
        $ecsConfig->sets($this->sets);
        $ecsConfig->paths($this->paths);
    }
    /**
     * @param string[] $paths
     */
    public function withPaths(array $paths) : self
    {
        $this->paths = $paths;
        return $this;
    }
    /**
     * Include PHP files from the root directory,
     * typically ecs.php, rector.php etc.
     */
    public function withRootFiles() : self
    {
        $rootPhpFilesFinder = (new Finder())->files()->in(\getcwd())->depth(0)->name('*.php');
        foreach ($rootPhpFilesFinder as $rootPhpFileFinder) {
            $this->paths[] = $rootPhpFileFinder->getRealPath();
        }
        return $this;
    }
    public function withPreparedSets(
        bool $psr12 = \false,
        bool $common = \false,
        bool $symplify = \false,
        // common sets
        bool $arrays = \false,
        bool $comments = \false,
        bool $docblocks = \false,
        bool $spaces = \false,
        bool $namespaces = \false,
        bool $controlStructures = \false,
        bool $phpunit = \false,
        bool $strict = \false
    ) : self
    {
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
    public function withSets(array $sets) : self
    {
        $this->sets = \array_merge($this->sets, $sets);
        return $this;
    }
}
