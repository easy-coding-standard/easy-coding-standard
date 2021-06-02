<?php

declare (strict_types=1);
namespace ECSPrefix20210602\Symplify\PackageBuilder\Console\Command;

use ECSPrefix20210602\Symfony\Component\Console\Command\Command;
use ECSPrefix20210602\Symfony\Component\Console\Input\InputOption;
use ECSPrefix20210602\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix20210602\Symplify\PackageBuilder\ValueObject\Option;
use ECSPrefix20210602\Symplify\SmartFileSystem\FileSystemGuard;
use ECSPrefix20210602\Symplify\SmartFileSystem\Finder\SmartFinder;
use ECSPrefix20210602\Symplify\SmartFileSystem\SmartFileSystem;
abstract class AbstractSymplifyCommand extends \ECSPrefix20210602\Symfony\Component\Console\Command\Command
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
        $this->addOption(\ECSPrefix20210602\Symplify\PackageBuilder\ValueObject\Option::CONFIG, 'c', \ECSPrefix20210602\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Path to config file');
    }
    /**
     * @required
     * @return void
     */
    public function autowireAbstractSymplifyCommand(\ECSPrefix20210602\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, \ECSPrefix20210602\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem, \ECSPrefix20210602\Symplify\SmartFileSystem\Finder\SmartFinder $smartFinder, \ECSPrefix20210602\Symplify\SmartFileSystem\FileSystemGuard $fileSystemGuard)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
        $this->smartFinder = $smartFinder;
        $this->fileSystemGuard = $fileSystemGuard;
    }
}
