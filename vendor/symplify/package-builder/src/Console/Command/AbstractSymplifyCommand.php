<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PackageBuilder\Console\Command;

use ConfigTransformer20210601\Symfony\Component\Console\Command\Command;
use ConfigTransformer20210601\Symfony\Component\Console\Input\InputOption;
use ConfigTransformer20210601\Symfony\Component\Console\Style\SymfonyStyle;
use ConfigTransformer20210601\Symplify\PackageBuilder\ValueObject\Option;
use ConfigTransformer20210601\Symplify\SmartFileSystem\FileSystemGuard;
use ConfigTransformer20210601\Symplify\SmartFileSystem\Finder\SmartFinder;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem;
abstract class AbstractSymplifyCommand extends \ConfigTransformer20210601\Symfony\Component\Console\Command\Command
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
        $this->addOption(\ConfigTransformer20210601\Symplify\PackageBuilder\ValueObject\Option::CONFIG, 'c', \ConfigTransformer20210601\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Path to config file');
    }
    /**
     * @required
     * @return void
     */
    public function autowireAbstractSymplifyCommand(\ConfigTransformer20210601\Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle, \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem, \ConfigTransformer20210601\Symplify\SmartFileSystem\Finder\SmartFinder $smartFinder, \ConfigTransformer20210601\Symplify\SmartFileSystem\FileSystemGuard $fileSystemGuard)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->smartFileSystem = $smartFileSystem;
        $this->smartFinder = $smartFinder;
        $this->fileSystemGuard = $fileSystemGuard;
    }
}
