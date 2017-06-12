<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\EasyCodingStandard\DependencyInjection\Extension\CheckersExtension;

final class CheckersBundle extends Bundle
{
    public function getContainerExtension(): CheckersExtension
    {
        return new CheckersExtension;
    }
}
