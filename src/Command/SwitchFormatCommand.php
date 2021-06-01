<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\Command;

use ConfigTransformer20210601\Symfony\Component\Console\Input\InputArgument;
use ConfigTransformer20210601\Symfony\Component\Console\Input\InputInterface;
use ConfigTransformer20210601\Symfony\Component\Console\Input\InputOption;
use ConfigTransformer20210601\Symfony\Component\Console\Output\OutputInterface;
use ConfigTransformer20210601\Symplify\ConfigTransformer\Configuration\Configuration;
use ConfigTransformer20210601\Symplify\ConfigTransformer\Converter\ConvertedContentFactory;
use ConfigTransformer20210601\Symplify\ConfigTransformer\FileSystem\ConfigFileDumper;
use ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option;
use ConfigTransformer20210601\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use ConfigTransformer20210601\Symplify\PackageBuilder\Console\ShellCode;
final class SwitchFormatCommand extends \ConfigTransformer20210601\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand
{
    /**
     * @var Configuration
     */
    private $configuration;
    /**
     * @var ConfigFileDumper
     */
    private $configFileDumper;
    /**
     * @var ConvertedContentFactory
     */
    private $convertedContentFactory;
    public function __construct(\ConfigTransformer20210601\Symplify\ConfigTransformer\Configuration\Configuration $configuration, \ConfigTransformer20210601\Symplify\ConfigTransformer\FileSystem\ConfigFileDumper $configFileDumper, \ConfigTransformer20210601\Symplify\ConfigTransformer\Converter\ConvertedContentFactory $convertedContentFactory)
    {
        parent::__construct();
        $this->configuration = $configuration;
        $this->configFileDumper = $configFileDumper;
        $this->convertedContentFactory = $convertedContentFactory;
    }
    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Converts XML/YAML configs to PHP format');
        $this->addArgument(\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option::SOURCES, \ConfigTransformer20210601\Symfony\Component\Console\Input\InputArgument::REQUIRED | \ConfigTransformer20210601\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'Path to directory with configs');
        $this->addOption(\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option::TARGET_SYMFONY_VERSION, 's', \ConfigTransformer20210601\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Symfony version to migrate config to', '3.2');
        $this->addOption(\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option::DRY_RUN, null, \ConfigTransformer20210601\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Dry run - no removal or config change');
    }
    protected function execute(\ConfigTransformer20210601\Symfony\Component\Console\Input\InputInterface $input, \ConfigTransformer20210601\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $this->configuration->populateFromInput($input);
        $suffixes = $this->configuration->getInputSuffixes();
        $suffixesRegex = '#\\.' . \implode('|', $suffixes) . '$#';
        $fileInfos = $this->smartFinder->find($this->configuration->getSource(), $suffixesRegex);
        $convertedContents = $this->convertedContentFactory->createFromFileInfos($fileInfos);
        foreach ($convertedContents as $convertedContent) {
            $this->configFileDumper->dumpFile($convertedContent);
        }
        if (!$this->configuration->isDryRun()) {
            $this->smartFileSystem->remove($fileInfos);
        }
        $successMessage = \sprintf('Processed %d file(s) to "PHP" format', \count($fileInfos));
        $this->symfonyStyle->success($successMessage);
        return \ConfigTransformer20210601\Symplify\PackageBuilder\Console\ShellCode::SUCCESS;
    }
}
