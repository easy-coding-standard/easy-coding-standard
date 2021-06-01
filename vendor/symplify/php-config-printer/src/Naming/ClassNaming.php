<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\Naming;

use ConfigTransformer20210601\Nette\Utils\Strings;
final class ClassNaming
{
    public function getShortName(string $class) : string
    {
        if (\ConfigTransformer20210601\Nette\Utils\Strings::contains($class, '\\')) {
            return (string) \ConfigTransformer20210601\Nette\Utils\Strings::after($class, '\\', -1);
        }
        return $class;
    }
}
