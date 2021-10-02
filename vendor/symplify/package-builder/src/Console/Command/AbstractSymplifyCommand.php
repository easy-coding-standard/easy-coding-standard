<?php

declare (strict_types=1);
namespace ECSPrefix20211002\Symplify\PackageBuilder\Console\Command;

use ECSPrefix20211002\Symfony\Component\Console\Command\Command;
use ECSPrefix20211002\Symfony\Component\Console\Input\InputOption;
use ECSPrefix20211002\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix20211002\Symfony\Contracts\Service\Attribute\Required;
use ECSPrefix20211002\Symplify\PackageBuilder\ValueObject\Option;
use ECSPrefix20211002\Symplify\SmartFileSystem\FileSystemGuard;
use ECSPrefix20211002\Symplify\SmartFileSystem\Finder\SmartFinder;
use ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileSystem;
abstract class AbstractSymplifyCommand extends \ECSPrefix20211002\Symfony\Component\Console\Command\Command
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    protected $symfonyStyle;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    protected $smartFileSystem;
    /**
     * @var \Symplify\SmartFileSystem\Finder\SmartFinder
     */
    protected $smartFinder;
    /**
     * @var \Symplify\SmartFileSystem\FileSystemGuard
     */
    protected $fileSystemGuard;
    public function __construct()
    {
        parent::__construct();
        $this->addOption(\ECSPrefix20211002\Symplify\PackageBuilder\ValueObject\Option::CONFIG, 'c', \ECSPrefix20211002\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Path to config file');
    }
    /**
     * @required
     */
    public function autowireAbstractSymplifyCommand(\ECSPrefix20211002\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, \ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem, \ECSPrefix20211002\Symplify\SmartFileSystem\Finder\SmartFinder $smartFinder, \ECSPrefix20211002\Symplify\SmartFileSystem\FileSystemGuard $fileSystemGuard) : void
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
        $this->smartFinder = $smartFinder;
        $this->fileSystemGuard = $fileSystemGuard;
    }
}
