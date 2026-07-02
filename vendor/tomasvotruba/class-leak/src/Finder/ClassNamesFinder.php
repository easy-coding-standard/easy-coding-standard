<?php

declare (strict_types=1);
namespace ECSPrefix202607\TomasVotruba\ClassLeak\Finder;

use Closure;
use ECSPrefix202607\TomasVotruba\ClassLeak\ClassNameResolver;
use ECSPrefix202607\TomasVotruba\ClassLeak\ValueObject\ClassNames;
use ECSPrefix202607\TomasVotruba\ClassLeak\ValueObject\FileWithClass;
final class ClassNamesFinder
{
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\ClassNameResolver
     */
    private $classNameResolver;
    public function __construct(ClassNameResolver $classNameResolver)
    {
        $this->classNameResolver = $classNameResolver;
    }
    /**
     * @param string[] $filePaths
     * @return FileWithClass[]
     */
    public function resolveClassNamesToCheck(array $filePaths, ?Closure $progressCallback): array
    {
        $filesWithClasses = [];
        foreach ($filePaths as $filePath) {
            ($nullsafeVariable1 = $progressCallback) ? $nullsafeVariable1->__invoke() : null;
            $classNames = $this->classNameResolver->resolveFromFilePath($filePath);
            if (!$classNames instanceof ClassNames) {
                continue;
            }
            $filesWithClasses[] = new FileWithClass($filePath, $classNames->getClassName(), $classNames->hasParentClassOrInterface(), $classNames->getAttributes());
        }
        return $filesWithClasses;
    }
}
