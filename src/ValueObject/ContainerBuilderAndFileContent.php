<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject;

use ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder;
final class ContainerBuilderAndFileContent
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;
    /**
     * @var string
     */
    private $fileContent;
    public function __construct(\ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $fileContent)
    {
        $this->containerBuilder = $containerBuilder;
        $this->fileContent = $fileContent;
    }
    public function getContainerBuilder() : \ConfigTransformer20210601\Symfony\Component\DependencyInjection\ContainerBuilder
    {
        return $this->containerBuilder;
    }
    public function getFileContent() : string
    {
        return $this->fileContent;
    }
}
