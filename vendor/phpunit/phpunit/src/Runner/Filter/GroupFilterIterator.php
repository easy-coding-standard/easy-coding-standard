<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\PHPUnit\Runner\Filter;

use function array_map;
use function array_merge;
use function in_array;
use function spl_object_hash;
use ECSPrefix20210803\PHPUnit\Framework\TestSuite;
use RecursiveFilterIterator;
use RecursiveIterator;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
abstract class GroupFilterIterator extends \RecursiveFilterIterator
{
    /**
     * @var string[]
     */
    protected $groupTests = [];
    public function __construct(\RecursiveIterator $iterator, array $groups, \ECSPrefix20210803\PHPUnit\Framework\TestSuite $suite)
    {
        parent::__construct($iterator);
        foreach ($suite->getGroupDetails() as $group => $tests) {
            if (\in_array((string) $group, $groups, \true)) {
                $testHashes = \array_map('spl_object_hash', $tests);
                $this->groupTests = \array_merge($this->groupTests, $testHashes);
            }
        }
    }
    public function accept() : bool
    {
        $test = $this->getInnerIterator()->current();
        if ($test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestSuite) {
            return \true;
        }
        return $this->doAccept(\spl_object_hash($test));
    }
    protected abstract function doAccept(string $hash);
}
