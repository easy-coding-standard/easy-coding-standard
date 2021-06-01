<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\Dummy;

use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface;
final class DummyYamlFileContentProvider implements \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface
{
    /**
     * @return void
     */
    public function setContent(string $yamlContent)
    {
    }
    public function getYamlContent() : string
    {
        return '';
    }
}
