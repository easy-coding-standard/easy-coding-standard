<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract\Console\Output;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface OutputFormatterInterface
{
    public function report(InputInterface $input, OutputInterface $output): int;

    public function getName(): string;
}
