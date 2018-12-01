<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Style;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class EasyCodingStandardStyleFactory
{
    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;

    /**
     * @var Terminal
     */
    private $terminal;

    public function __construct(Terminal $terminal)
    {
        $this->privatesCaller = new PrivatesCaller();
        $this->terminal = $terminal;
    }

    public function create(): EasyCodingStandardStyle
    {
        $input = new ArgvInput();
        $output = new ConsoleOutput();

        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        $this->privatesCaller->callPrivateMethod(new Application(), 'configureIO', $input, $output);

        // --debug is called
        if ($input->hasParameterOption('--debug')) {
            $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }

        return new EasyCodingStandardStyle($input, $output, $this->terminal);
    }
}
