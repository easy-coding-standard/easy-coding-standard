<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract;

interface YamlFileContentProviderInterface
{
    /**
     * @return void
     */
    public function setContent(string $yamlContent);
    public function getYamlContent() : string;
}
