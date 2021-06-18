<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Application;

final class AppliedCheckersCollector
{
    /**
     * @var array<class-string|string>
     */
    private $appliedCheckerClasses = [];
    /**
     * @param class-string|string $checkerClass
     * @return void
     */
    public function addAppliedCheckerClass(string $checkerClass)
    {
        $this->appliedCheckerClasses[] = $checkerClass;
    }
    /**
     * @return array<class-string|string>
     */
    public function getAppliedCheckerClasses() : array
    {
        return $this->appliedCheckerClasses;
    }
    /**
     * @return void
     */
    public function resetAppliedCheckerClasses()
    {
        $this->appliedCheckerClasses = [];
    }
}
