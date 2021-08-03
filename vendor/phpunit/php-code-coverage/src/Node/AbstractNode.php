<?php

declare (strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\SebastianBergmann\CodeCoverage\Node;

use const DIRECTORY_SEPARATOR;
use function array_merge;
use function str_replace;
use function substr;
use Countable;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage;
use ECSPrefix20210803\SebastianBergmann\LinesOfCode\LinesOfCode;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
abstract class AbstractNode implements \Countable
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $pathAsString;
    /**
     * @var array
     */
    private $pathAsArray;
    /**
     * @var AbstractNode
     */
    private $parent;
    /**
     * @var string
     */
    private $id;
    public function __construct(string $name, self $parent = null)
    {
        if (\substr($name, -1) === \DIRECTORY_SEPARATOR) {
            $name = \substr($name, 0, -1);
        }
        $this->name = $name;
        $this->parent = $parent;
    }
    public function name() : string
    {
        return $this->name;
    }
    public function id() : string
    {
        if ($this->id === null) {
            $parent = $this->parent();
            if ($parent === null) {
                $this->id = 'index';
            } else {
                $parentId = $parent->id();
                if ($parentId === 'index') {
                    $this->id = \str_replace(':', '_', $this->name);
                } else {
                    $this->id = $parentId . '/' . $this->name;
                }
            }
        }
        return $this->id;
    }
    public function pathAsString() : string
    {
        if ($this->pathAsString === null) {
            if ($this->parent === null) {
                $this->pathAsString = $this->name;
            } else {
                $this->pathAsString = $this->parent->pathAsString() . \DIRECTORY_SEPARATOR . $this->name;
            }
        }
        return $this->pathAsString;
    }
    public function pathAsArray() : array
    {
        if ($this->pathAsArray === null) {
            if ($this->parent === null) {
                $this->pathAsArray = [];
            } else {
                $this->pathAsArray = $this->parent->pathAsArray();
            }
            $this->pathAsArray[] = $this;
        }
        return $this->pathAsArray;
    }
    public function parent() : ?self
    {
        return $this->parent;
    }
    public function percentageOfTestedClasses() : \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage
    {
        return \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage::fromFractionAndTotal($this->numberOfTestedClasses(), $this->numberOfClasses());
    }
    public function percentageOfTestedTraits() : \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage
    {
        return \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage::fromFractionAndTotal($this->numberOfTestedTraits(), $this->numberOfTraits());
    }
    public function percentageOfTestedClassesAndTraits() : \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage
    {
        return \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage::fromFractionAndTotal($this->numberOfTestedClassesAndTraits(), $this->numberOfClassesAndTraits());
    }
    public function percentageOfTestedFunctions() : \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage
    {
        return \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage::fromFractionAndTotal($this->numberOfTestedFunctions(), $this->numberOfFunctions());
    }
    public function percentageOfTestedMethods() : \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage
    {
        return \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage::fromFractionAndTotal($this->numberOfTestedMethods(), $this->numberOfMethods());
    }
    public function percentageOfTestedFunctionsAndMethods() : \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage
    {
        return \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage::fromFractionAndTotal($this->numberOfTestedFunctionsAndMethods(), $this->numberOfFunctionsAndMethods());
    }
    public function percentageOfExecutedLines() : \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage
    {
        return \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage::fromFractionAndTotal($this->numberOfExecutedLines(), $this->numberOfExecutableLines());
    }
    public function percentageOfExecutedBranches() : \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage
    {
        return \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage::fromFractionAndTotal($this->numberOfExecutedBranches(), $this->numberOfExecutableBranches());
    }
    public function percentageOfExecutedPaths() : \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage
    {
        return \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Percentage::fromFractionAndTotal($this->numberOfExecutedPaths(), $this->numberOfExecutablePaths());
    }
    public function numberOfClassesAndTraits() : int
    {
        return $this->numberOfClasses() + $this->numberOfTraits();
    }
    public function numberOfTestedClassesAndTraits() : int
    {
        return $this->numberOfTestedClasses() + $this->numberOfTestedTraits();
    }
    public function classesAndTraits() : array
    {
        return \array_merge($this->classes(), $this->traits());
    }
    public function numberOfFunctionsAndMethods() : int
    {
        return $this->numberOfFunctions() + $this->numberOfMethods();
    }
    public function numberOfTestedFunctionsAndMethods() : int
    {
        return $this->numberOfTestedFunctions() + $this->numberOfTestedMethods();
    }
    public abstract function classes() : array;
    public abstract function traits() : array;
    public abstract function functions() : array;
    public abstract function linesOfCode() : \ECSPrefix20210803\SebastianBergmann\LinesOfCode\LinesOfCode;
    public abstract function numberOfExecutableLines() : int;
    public abstract function numberOfExecutedLines() : int;
    public abstract function numberOfExecutableBranches() : int;
    public abstract function numberOfExecutedBranches() : int;
    public abstract function numberOfExecutablePaths() : int;
    public abstract function numberOfExecutedPaths() : int;
    public abstract function numberOfClasses() : int;
    public abstract function numberOfTestedClasses() : int;
    public abstract function numberOfTraits() : int;
    public abstract function numberOfTestedTraits() : int;
    public abstract function numberOfMethods() : int;
    public abstract function numberOfTestedMethods() : int;
    public abstract function numberOfFunctions() : int;
    public abstract function numberOfTestedFunctions() : int;
}
