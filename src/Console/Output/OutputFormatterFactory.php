<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;

final class OutputFormatterFactory
{
    public function create(ContainerInterface $container): OutputFormatterInterface
    {
        return $container->get(TableOutputFormatter::class);
    }
}
