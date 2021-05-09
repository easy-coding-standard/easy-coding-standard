<?php

declare (strict_types=1);
namespace Symplify\EasyTesting\Tests\MissingSkipPrefixResolver;

use Symplify\EasyTesting\Finder\FixtureFinder;
use Symplify\EasyTesting\HttpKernel\EasyTestingKernel;
use Symplify\EasyTesting\MissplacedSkipPrefixResolver;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
final class MissingSkipPrefixResolverTest extends \Symplify\PackageBuilder\Testing\AbstractKernelTestCase
{
    /**
     * @var MissplacedSkipPrefixResolver
     */
    private $missplacedSkipPrefixResolver;
    /**
     * @var FixtureFinder
     */
    private $fixtureFinder;
    protected function setUp() : void
    {
        $this->bootKernel(\Symplify\EasyTesting\HttpKernel\EasyTestingKernel::class);
        $this->missplacedSkipPrefixResolver = $this->getService(\Symplify\EasyTesting\MissplacedSkipPrefixResolver::class);
        $this->fixtureFinder = $this->getService(\Symplify\EasyTesting\Finder\FixtureFinder::class);
    }
    public function test() : void
    {
        $fileInfos = $this->fixtureFinder->find([__DIR__ . '/Fixture']);
        $invalidFileInfos = $this->missplacedSkipPrefixResolver->resolve($fileInfos);
        $this->assertCount(2, $invalidFileInfos);
    }
}
