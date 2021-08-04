<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\Prophecy\Comparator;

use ECSPrefix20210804\SebastianBergmann\Comparator\Factory as BaseFactory;
/**
 * Prophecy comparator factory.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class Factory extends \ECSPrefix20210804\SebastianBergmann\Comparator\Factory
{
    /**
     * @var Factory
     */
    private static $instance;
    public function __construct()
    {
        parent::__construct();
        $this->register(new \ECSPrefix20210804\Prophecy\Comparator\ClosureComparator());
        $this->register(new \ECSPrefix20210804\Prophecy\Comparator\ProphecyComparator());
    }
    /**
     * @return Factory
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new \ECSPrefix20210804\Prophecy\Comparator\Factory();
        }
        return self::$instance;
    }
}
