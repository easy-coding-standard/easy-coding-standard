<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Finder\CheckerClassFinder;
use Symplify\PackageBuilder\Composer\VendorDirProvider;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class FindCommand extends Command
{
    /**
     * @var string
     */
    private const ARGUMENT_NAME = 'name';

    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

    /**
     * @var CheckerClassFinder
     */
    private $checkerClassFinder;

    public function __construct(
        EasyCodingStandardStyle $easyCodingStandardStyle,
        CheckerClassFinder $checkerClassFinder
    ) {
        parent::__construct();

        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->checkerClassFinder = $checkerClassFinder;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Show all available checkers');
        $this->addArgument(
            self::ARGUMENT_NAME,
            InputOption::VALUE_REQUIRED,
            'Filter checkers by name, e.g. "array" or "Symplify"'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $checkers = $this->checkerClassFinder->findInDirectories([
            getcwd() . '/src',
            getcwd() . '/packages',
            VendorDirProvider::provide(),
        ]);

        /** @var string $name */
        $name = $input->getArgument(self::ARGUMENT_NAME);

        if ($name) {
            $checkers = $this->filterCheckersByName($checkers, $name);
        }

        if (! count($checkers)) {
            $message = 'No checkers found';
            if ($name) {
                $message .= sprintf(' for "%s" name', $name);
            }

            $this->easyCodingStandardStyle->note($message);

            return ShellCode::SUCCESS;
        }

        sort($checkers);
        $this->easyCodingStandardStyle->listing($checkers);

        $this->easyCodingStandardStyle->success(sprintf('Found %d checkers', count($checkers)));

        return ShellCode::SUCCESS;
    }

    /**
     * @param string[] $checkers
     * @return string[]
     */
    private function filterCheckersByName(array $checkers, string $name): array
    {
        $filteredCheckers = [];
        foreach ($checkers as $checker) {
            if (Strings::match($checker, sprintf('#%s#i', preg_quote($name)))) {
                $filteredCheckers[] = $checker;
            }
        }

        return $filteredCheckers;
    }
}
