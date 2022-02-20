<?php

declare (strict_types=1);
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
use PhpCsFixer\Console\Report\ListSetsReport\ReporterInterface;
use PhpCsFixer\Console\Report\ListSetsReport\ReportSummary;
use PhpCsFixer\Console\Report\ListSetsReport\TextReporter;
use PhpCsFixer\RuleSet\RuleSets;
use ECSPrefix20220220\Symfony\Component\Console\Command\Command;
use ECSPrefix20220220\Symfony\Component\Console\Formatter\OutputFormatter;
use ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20220220\Symfony\Component\Console\Input\InputOption;
use ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ListSetsCommand extends \ECSPrefix20220220\Symfony\Component\Console\Command\Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'list-sets';
    /**
     * {@inheritdoc}
     */
    protected function configure() : void
    {
        $this->setDefinition([new \ECSPrefix20220220\Symfony\Component\Console\Input\InputOption('format', '', \ECSPrefix20220220\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'To output results in other formats.', (new \PhpCsFixer\Console\Report\ListSetsReport\TextReporter())->getFormat())])->setDescription('List all available RuleSets.');
    }
    protected function execute(\ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $reporter = $this->resolveReporterWithFactory($input->getOption('format'), new \PhpCsFixer\Console\Report\ListSetsReport\ReporterFactory());
        $reportSummary = new \PhpCsFixer\Console\Report\ListSetsReport\ReportSummary(\array_values(\PhpCsFixer\RuleSet\RuleSets::getSetDefinitions()));
        $report = $reporter->generate($reportSummary);
        $output->isDecorated() ? $output->write(\ECSPrefix20220220\Symfony\Component\Console\Formatter\OutputFormatter::escape($report)) : $output->write($report, \false, \ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface::OUTPUT_RAW);
        return 0;
    }
    private function resolveReporterWithFactory(string $format, \PhpCsFixer\Console\Report\ListSetsReport\ReporterFactory $factory) : \PhpCsFixer\Console\Report\ListSetsReport\ReporterInterface
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
