<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\Yaml;

use ConfigTransformer20210601\Symfony\Component\Yaml\Yaml;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo;
final class YamlImportsFactory
{
    public function createFromPhpFileInfo(\ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo $fileInfo) : string
    {
        $yamlImportData = ['imports' => [['resource' => $fileInfo->getBasenameWithoutSuffix() . '.php']]];
        return \ConfigTransformer20210601\Symfony\Component\Yaml\Yaml::dump($yamlImportData) . \PHP_EOL;
    }
}
