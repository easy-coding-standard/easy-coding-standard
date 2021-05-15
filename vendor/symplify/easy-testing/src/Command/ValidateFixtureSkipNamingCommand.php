<?php

namespace ECSPrefix20210515\Symplify\EasyTesting\Command;

use ECSPrefix20210515\Symfony\Component\Console\Input\InputArgument;
use ECSPrefix20210515\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210515\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20210515\Symplify\EasyTesting\Finder\FixtureFinder;
use ECSPrefix20210515\Symplify\EasyTesting\MissplacedSkipPrefixResolver;
use ECSPrefix20210515\Symplify\EasyTesting\ValueObject\Option;
use ECSPrefix20210515\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use ECSPrefix20210515\Symplify\PackageBuilder\Console\ShellCode;
final class ValidateFixtureSkipNamingCommand extends \ECSPrefix20210515\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var MissplacedSkipPrefixResolver
     */
    private $missplacedSkipPrefixResolver;
    /**
     * @var FixtureFinder
     */
    private $fixtureFinder;
    public function __construct(\ECSPrefix20210515\Symplify\EasyTesting\MissplacedSkipPrefixResolver $missplacedSkipPrefixResolver, \ECSPrefix20210515\Symplify\EasyTesting\Finder\FixtureFinder $fixtureFinder)
    {
        $this->missplacedSkipPrefixResolver = $missplacedSkipPrefixResolver;
        $this->fixtureFinder = $fixtureFinder;
        parent::__construct();
    }
    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument(\ECSPrefix20210515\Symplify\EasyTesting\ValueObject\Option::SOURCE, \ECSPrefix20210515\Symfony\Component\Console\Input\InputArgument::REQUIRED | \ECSPrefix20210515\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'Paths to analyse');
        $this->setDescription('Check that skipped fixture files (without `-----` separator) have a "skip" prefix');
    }
    /**
     * @return int
     */
    protected function execute(\ECSPrefix20210515\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20210515\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $source = (array) $input->getArgument(\ECSPrefix20210515\Symplify\EasyTesting\ValueObject\Option::SOURCE);
        $fixtureFileInfos = $this->fixtureFinder->find($source);
        $missplacedFixtureFileInfos = $this->missplacedSkipPrefixResolver->resolve($fixtureFileInfos);
        if ($missplacedFixtureFileInfos === []) {
            $message = \sprintf('All %d fixture files have valid names', \count($fixtureFileInfos));
            $this->symfonyStyle->success($message);
            return \ECSPrefix20210515\Symplify\PackageBuilder\Console\ShellCode::SUCCESS;
        }
        foreach ($missplacedFixtureFileInfos['incorrect_skips'] as $missplacedFixtureFileInfo) {
            $errorMessage = \sprintf('The file "%s" should drop the "skip/keep" prefix', $missplacedFixtureFileInfo->getRelativeFilePathFromCwd());
            $this->symfonyStyle->note($errorMessage);
        }
        foreach ($missplacedFixtureFileInfos['missing_skips'] as $missplacedFixtureFileInfo) {
            $errorMessage = \sprintf('The file "%s" should start with "skip/keep" prefix', $missplacedFixtureFileInfo->getRelativeFilePathFromCwd());
            $this->symfonyStyle->note($errorMessage);
        }
        $countError = \count($missplacedFixtureFileInfos['incorrect_skips']) + \count($missplacedFixtureFileInfos['missing_skips']);
        if ($countError === 0) {
            $message = \sprintf('All %d fixture files have valid names', \count($fixtureFileInfos));
            $this->symfonyStyle->success($message);
            return \ECSPrefix20210515\Symplify\PackageBuilder\Console\ShellCode::SUCCESS;
        }
        $errorMessage = \sprintf('Found %d test file fixtures with wrong prefix', $countError);
        $this->symfonyStyle->error($errorMessage);
        return \ECSPrefix20210515\Symplify\PackageBuilder\Console\ShellCode::ERROR;
    }
}
