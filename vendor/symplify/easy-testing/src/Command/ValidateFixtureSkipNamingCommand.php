<?php

declare (strict_types=1);
namespace ECSPrefix20220220\Symplify\EasyTesting\Command;

use ECSPrefix20220220\Symfony\Component\Console\Input\InputArgument;
use ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20220220\Symplify\EasyTesting\Finder\FixtureFinder;
use ECSPrefix20220220\Symplify\EasyTesting\MissplacedSkipPrefixResolver;
use ECSPrefix20220220\Symplify\EasyTesting\ValueObject\Option;
use ECSPrefix20220220\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
final class ValidateFixtureSkipNamingCommand extends \ECSPrefix20220220\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var \Symplify\EasyTesting\MissplacedSkipPrefixResolver
     */
    private $missplacedSkipPrefixResolver;
    /**
     * @var \Symplify\EasyTesting\Finder\FixtureFinder
     */
    private $fixtureFinder;
    public function __construct(\ECSPrefix20220220\Symplify\EasyTesting\MissplacedSkipPrefixResolver $missplacedSkipPrefixResolver, \ECSPrefix20220220\Symplify\EasyTesting\Finder\FixtureFinder $fixtureFinder)
    {
        $this->missplacedSkipPrefixResolver = $missplacedSkipPrefixResolver;
        $this->fixtureFinder = $fixtureFinder;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('validate-fixture-skip-naming');
        $this->addArgument(\ECSPrefix20220220\Symplify\EasyTesting\ValueObject\Option::SOURCE, \ECSPrefix20220220\Symfony\Component\Console\Input\InputArgument::REQUIRED | \ECSPrefix20220220\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'Paths to analyse');
        $this->setDescription('Check that skipped fixture files (without `-----` separator) have a "skip" prefix');
    }
    protected function execute(\ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $source = (array) $input->getArgument(\ECSPrefix20220220\Symplify\EasyTesting\ValueObject\Option::SOURCE);
        $fixtureFileInfos = $this->fixtureFinder->find($source);
        $missplacedFixtureFileInfos = $this->missplacedSkipPrefixResolver->resolve($fixtureFileInfos);
        if ($missplacedFixtureFileInfos === []) {
            $message = \sprintf('All %d fixture files have valid names', \count($fixtureFileInfos));
            $this->symfonyStyle->success($message);
            return self::SUCCESS;
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
            return self::SUCCESS;
        }
        $errorMessage = \sprintf('Found %d test file fixtures with wrong prefix', $countError);
        $this->symfonyStyle->error($errorMessage);
        return self::FAILURE;
    }
}
