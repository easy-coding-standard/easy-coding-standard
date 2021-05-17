<?php

namespace ECSPrefix20210517\Symplify\SetConfigResolver;

use ECSPrefix20210517\Symplify\SetConfigResolver\Config\SetsParameterResolver;
use ECSPrefix20210517\Symplify\SetConfigResolver\Contract\SetProviderInterface;
use ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\SetConfigResolver\Tests\ConfigResolver\SetAwareConfigResolverTest
 */
final class SetAwareConfigResolver extends \ECSPrefix20210517\Symplify\SetConfigResolver\AbstractConfigResolver
{
    /**
     * @var SetsParameterResolver
     */
    private $setsParameterResolver;
    public function __construct(\ECSPrefix20210517\Symplify\SetConfigResolver\Contract\SetProviderInterface $setProvider)
    {
        $setResolver = new \ECSPrefix20210517\Symplify\SetConfigResolver\SetResolver($setProvider);
        $this->setsParameterResolver = new \ECSPrefix20210517\Symplify\SetConfigResolver\Config\SetsParameterResolver($setResolver);
        parent::__construct();
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return mixed[]
     */
    public function resolveFromParameterSetsFromConfigFiles(array $fileInfos)
    {
        return $this->setsParameterResolver->resolveFromFileInfos($fileInfos);
    }
}
