<?php

declare (strict_types=1);
namespace ECSPrefix20220130\Symplify\ConsoleColorDiff\Diff\Output;

use ECSPrefix20220130\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use ECSPrefix20220130\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
/**
 * @api
 * Creates @see UnifiedDiffOutputBuilder with "$contextLines = 1000;"
 */
final class CompleteUnifiedDiffOutputBuilderFactory
{
    /**
     * @var \Symplify\PackageBuilder\Reflection\PrivatesAccessor
     */
    private $privatesAccessor;
    public function __construct(\ECSPrefix20220130\Symplify\PackageBuilder\Reflection\PrivatesAccessor $privatesAccessor)
    {
        $this->privatesAccessor = $privatesAccessor;
    }
    /**
     * @api
     */
    public function create() : \ECSPrefix20220130\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder
    {
        $unifiedDiffOutputBuilder = new \ECSPrefix20220130\SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder('');
        $this->privatesAccessor->setPrivateProperty($unifiedDiffOutputBuilder, 'contextLines', 10000);
        return $unifiedDiffOutputBuilder;
    }
}
