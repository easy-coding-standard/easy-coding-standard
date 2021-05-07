<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Console\Command;

use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\Console\Report\ListSetsReport\ReporterFactory;
use PhpCsFixer\Console\Report\ListSetsReport\ReportSummary;
use PhpCsFixer\Console\Report\ListSetsReport\TextReporter;
use PhpCsFixer\RuleSet\RuleSets;
use ECSPrefix20210507\Symfony\Component\Console\Command\Command;
use ECSPrefix20210507\Symfony\Component\Console\Formatter\OutputFormatter;
use ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210507\Symfony\Component\Console\Input\InputOption;
use ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ListSetsCommand extends \ECSPrefix20210507\Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'list-sets';
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function configure()
    {
        $this->setDefinition([new \ECSPrefix20210507\Symfony\Component\Console\Input\InputOption('format', '', \ECSPrefix20210507\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'To output results in other formats.', (new \PhpCsFixer\Console\Report\ListSetsReport\TextReporter())->getFormat())])->setDescription('List all available RuleSets.');
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     * @param \ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute($input, $output)
    {
        $reporter = $this->resolveReporterWithFactory($input->getOption('format'), new \PhpCsFixer\Console\Report\ListSetsReport\ReporterFactory());
        $reportSummary = new \PhpCsFixer\Console\Report\ListSetsReport\ReportSummary(\array_values(\PhpCsFixer\RuleSet\RuleSets::getSetDefinitions()));
        $report = $reporter->generate($reportSummary);
        $output->isDecorated() ? $output->write(\ECSPrefix20210507\Symfony\Component\Console\Formatter\OutputFormatter::escape($report)) : $output->write($report, \false, \ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface::OUTPUT_RAW);
        return 0;
    }
    /**
     * @param string $format
     * @param \PhpCsFixer\Console\Report\ListSetsReport\ReporterFactory $factory
     */
    private function resolveReporterWithFactory($format, $factory)
    {
        try {
            $factory->registerBuiltInReporters();
            $reporter = $factory->getReporter($format);
        } catch (\UnexpectedValueException $e) {
            $formats = $factory->getFormats();
            \sort($formats);
            throw new \PhpCsFixer\ConfigurationException\InvalidConfigurationException(\sprintf('The format "%s" is not defined, supported are "%s".', $format, \implode('", "', $formats)));
        }
        return $reporter;
    }
}
