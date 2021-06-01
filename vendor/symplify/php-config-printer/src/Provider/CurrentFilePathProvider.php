<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\Provider;

final class CurrentFilePathProvider
{
    /**
     * @var string|null
     */
    private $filePath;
    /**
     * @return void
     */
    public function setFilePath(string $yamlFilePath)
    {
        $this->filePath = $yamlFilePath;
    }
    /**
     * @return string|null
     */
    public function getFilePath()
    {
        return $this->filePath;
    }
}
