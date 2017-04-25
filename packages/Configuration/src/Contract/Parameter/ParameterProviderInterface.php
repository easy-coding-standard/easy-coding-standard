<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Contract\Parameter;

interface ParameterProviderInterface
{
    /**
     * @return mixed[]
     */
    public function provide(): array;
}
