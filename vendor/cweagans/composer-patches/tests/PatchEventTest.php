<?php

/**
 * @file
 * Tests event dispatching.
 */
namespace ECSPrefix20220415\cweagans\Composer\Tests;

use ECSPrefix20220415\cweagans\Composer\PatchEvent;
use ECSPrefix20220415\cweagans\Composer\PatchEvents;
use ECSPrefix20220415\Composer\Package\PackageInterface;
class PatchEventTest extends \ECSPrefix20220415\PHPUnit_Framework_TestCase
{
    /**
     * Tests all the getters.
     *
     * @dataProvider patchEventDataProvider
     */
    public function testGetters($event_name, \ECSPrefix20220415\Composer\Package\PackageInterface $package, $url, $description)
    {
        $patch_event = new \ECSPrefix20220415\cweagans\Composer\PatchEvent($event_name, $package, $url, $description);
        $this->assertEquals($event_name, $patch_event->getName());
        $this->assertEquals($package, $patch_event->getPackage());
        $this->assertEquals($url, $patch_event->getUrl());
        $this->assertEquals($description, $patch_event->getDescription());
    }
    public function patchEventDataProvider()
    {
        $prophecy = $this->prophesize('ECSPrefix20220415\\Composer\\Package\\PackageInterface');
        $package = $prophecy->reveal();
        return array(array(\ECSPrefix20220415\cweagans\Composer\PatchEvents::PRE_PATCH_APPLY, $package, 'https://www.drupal.org', 'A test patch'), array(\ECSPrefix20220415\cweagans\Composer\PatchEvents::POST_PATCH_APPLY, $package, 'https://www.drupal.org', 'A test patch'));
    }
}
