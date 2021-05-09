<?php

namespace Symplify\PackageBuilder\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\ValueObject\Option;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileSystem;

abstract class AbstractSymplifyCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    protected $symfonyStyle;

    /**
     * @var SmartFileSystem
     */
    protected $smartFileSystem;

    /**
     * @var SmartFinder
     */
    protected $smartFinder;

    /**
     * @var FileSystemGuard
     */
    protected $fileSystemGuard;

    public function __construct()
    {
        parent::__construct();

        $this->addOption(Option::CONFIG, 'c', InputOption::VALUE_REQUIRED, 'Path to config file');
    }

    /**
     * @required
     * @return void
     */
    public function autowireAbstractSymplifyCommand(
        SymfonyStyle $symfonyStyle,
        SmartFileSystem $smartFileSystem,
        SmartFinder $smartFinder,
        FileSystemGuard $fileSystemGuard
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
        $this->smartFinder = $smartFinder;
        $this->fileSystemGuard = $fileSystemGuard;
    }
}
