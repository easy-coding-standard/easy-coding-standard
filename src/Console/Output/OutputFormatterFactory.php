<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\Configuration\Option;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;

final class OutputFormatterFactory
{
    public static function create(ContainerInterface $container, InputInterface $input): OutputFormatterInterface
    {
        $formatOption = $input->getOption(Option::OUTPUT_FORMAT_OPTION);

        if ($formatOption === Option::JSON_OUTPUT_FORMAT) {
            return $container->get(JsonOutputFormatter::class);
        }

        return $container->get(TableOutputFormatter::class);
    }
}
