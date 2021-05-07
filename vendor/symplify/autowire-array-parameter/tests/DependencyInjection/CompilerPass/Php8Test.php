<?php

declare (strict_types=1);
namespace Symplify\AutowireArrayParameter\Tests\DependencyInjection\CompilerPass;

use Symplify\AutowireArrayParameter\Tests\HttpKernel\AutowireArrayParameterHttpKernel;
use Symplify\AutowireArrayParameter\Tests\SourcePhp8\PromotedPropertyCollector;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
/**
 * @requires PHP 8.0
 */
final class Php8Test extends \Symplify\PackageBuilder\Testing\AbstractKernelTestCase
{
    protected function setUp() : void
    {
        $this->bootKernelWithConfigs(\Symplify\AutowireArrayParameter\Tests\HttpKernel\AutowireArrayParameterHttpKernel::class, [__DIR__ . '/../../config/php8_config.php']);
    }
    public function test() : void
    {
        $promotedPropertyCollector = $this->getService(\Symplify\AutowireArrayParameter\Tests\SourcePhp8\PromotedPropertyCollector::class);
        $this->assertCount(3, $promotedPropertyCollector->getFirstCollected());
    }
}
