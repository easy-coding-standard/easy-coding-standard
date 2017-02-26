<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard;

use Symplify\EasyCodingStandard\Error\ErrorFilter;

final class Skipper
{
    /**
     * @var ErrorFilter
     */
    private $errorFilter;

    public function __construct(ErrorFilter $errorFilter)
    {
        $this->errorFilter = $errorFilter;
    }

    public function isFileRuleSkippedForSourceClass(string $file, string $sourceClass)
    {
        dump($file, $sourceClass);
        die;
    }
}
