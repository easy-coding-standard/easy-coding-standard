<?php

declare (strict_types=1);
namespace ECSPrefix202607\TomasVotruba\ClassLeak\ValueObject;

final class UnusedClassesResult
{
    /**
     * @var FileWithClass[]
     * @readonly
     */
    private $parentLessFileWithClasses;
    /**
     * @var FileWithClass[]
     * @readonly
     */
    private $withParentsFileWithClasses;
    /**
     * @var FileWithClass[]
     * @readonly
     */
    private $traits;
    /**
     * @param FileWithClass[] $withParentsFileWithClasses
     * @param FileWithClass[] $parentLessFileWithClasses
     * @param FileWithClass[] $traits
     */
    public function __construct(array $parentLessFileWithClasses, array $withParentsFileWithClasses, array $traits)
    {
        $this->parentLessFileWithClasses = $parentLessFileWithClasses;
        $this->withParentsFileWithClasses = $withParentsFileWithClasses;
        $this->traits = $traits;
    }
    /**
     * @return FileWithClass[]
     */
    public function getParentLessFileWithClasses(): array
    {
        return $this->parentLessFileWithClasses;
    }
    /**
     * @return FileWithClass[]
     */
    public function getWithParentsFileWithClasses(): array
    {
        return $this->withParentsFileWithClasses;
    }
    public function getCount(): int
    {
        return count($this->parentLessFileWithClasses) + count($this->withParentsFileWithClasses) + count($this->traits);
    }
    /**
     * @return FileWithClass[]
     */
    public function getTraits(): array
    {
        return $this->traits;
    }
}
