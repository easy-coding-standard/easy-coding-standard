<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class ValidateCommand extends Command
{
    /**
     * @var string
     */
    private const SERVICES_IN_CONFIG_PATTERN = '#^\s{4}+(?<service>[A-Z][A-Za-z_\\\\]+):#sm';

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(Configuration $configuration, EasyCodingStandardStyle $easyCodingStandardStyle)
    {
        parent::__construct();

        $this->configuration = $configuration;
        $this->symfonyStyle = $easyCodingStandardStyle;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rootConfig = $this->configuration->getConfigFilePath();

        if (! file_exists($rootConfig)) {
            return ShellCode::SUCCESS;
        }

        // 1. check rules loaded from imported files
        $imports = $this->findImportsFromFilePath($rootConfig);
        if (count($imports) === 0) {
            return ShellCode::SUCCESS;
        }

        $importedServices = [];
        foreach ($imports as $import) {
            $newlyImportedServices = $this->findServicesInConfigFilePath($import);
            $importedServices = array_merge($importedServices, $newlyImportedServices);
        }

        // 2. check explicit local rules
        $rootServices = $this->findServicesInConfigFilePath($rootConfig);

        $duplicatedServices = array_intersect($rootServices, $importedServices);

        foreach ($duplicatedServices as $duplicatedService) {
            $this->symfonyStyle->warning(sprintf(
                '"%s" might be duplicated in your "%s" config',
                $duplicatedService,
                $rootConfig
            ));
        }

        return ShellCode::SUCCESS;
    }

    /**
     * @return string[]
     */
    private function findServicesInConfigFilePath(string $filePath): array
    {
        $configFileContent = FileSystem::read($filePath);

        $services = [];
        $matches = Strings::matchAll($configFileContent, self::SERVICES_IN_CONFIG_PATTERN);

        foreach ($matches as $match) {
            $service = (string) $match['service'];
            if (class_exists($service)) {
                $services[] = $service;
            }
        }

        return $services;
    }

    /**
     * @return string[]
     */
    private function findImportsFromFilePath(string $filePath): array
    {
        $configFileContent = FileSystem::read($filePath);

        $files = [];

        $matches = Strings::matchAll($configFileContent, '#resource\:\s+(\'|")?(?<config>.*?\.yaml)#sm');
        foreach ($matches as $match) {
            $config = (string) $match['config'];
            if (file_exists($config)) {
                $files[] = $config;
            }
        }

        return $files;
    }
}
