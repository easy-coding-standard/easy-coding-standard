<?php

declare (strict_types=1);
namespace ECSPrefix202608\TomasVotruba\ClassLeak\ValueObject;

final class ClassNames
{
    /**
     * @readonly
     * @var string
     */
    private $className;
    /**
     * @readonly
     * @var bool
     */
    private $hasParentClassOrInterface;
    /**
     * @var string[]
     * @readonly
     */
    private $attributes;
    /**
     * @param string[] $attributes
     */
    public function __construct(string $className, bool $hasParentClassOrInterface, array $attributes)
    {
        $this->className = $className;
        $this->hasParentClassOrInterface = $hasParentClassOrInterface;
        $this->attributes = $attributes;
    }
    public function getClassName(): string
    {
        return $this->className;
    }
    public function hasParentClassOrInterface(): bool
    {
        return $this->hasParentClassOrInterface;
    }
    /**
     * @return string[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
