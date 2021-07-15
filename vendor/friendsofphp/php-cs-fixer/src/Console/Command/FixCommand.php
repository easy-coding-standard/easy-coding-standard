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

use PhpCsFixer\Config;
use PhpCsFixer\ConfigInterface;
use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\Console\ConfigurationResolver;
use PhpCsFixer\Console\Output\ErrorOutput;
use PhpCsFixer\Console\Output\NullOutput;
use PhpCsFixer\Console\Output\ProcessOutput;
use PhpCsFixer\Console\Report\FixReport\ReportSummary;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Runner\Runner;
use PhpCsFixer\ToolInfoInterface;
use ECSPrefix20210715\Symfony\Component\Console\Command\Command;
use ECSPrefix20210715\Symfony\Component\Console\Input\InputArgument;
use ECSPrefix20210715\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210715\Symfony\Component\Console\Input\InputOption;
use ECSPrefix20210715\Symfony\Component\Console\Output\ConsoleOutputInterface;
use ECSPrefix20210715\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20210715\Symfony\Component\Console\Terminal;
use ECSPrefix20210715\Symfony\Component\EventDispatcher\EventDispatcher;
use ECSPrefix20210715\Symfony\Component\EventDispatcher\EventDispatcherInterface;
use ECSPrefix20210715\Symfony\Component\Stopwatch\Stopwatch;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FixCommand extends \ECSPrefix20210715\Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'fix';
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var ErrorsManager
     */
    private $errorsManager;
    /**
     * @var Stopwatch
     */
    private $stopwatch;
    /**
     * @var ConfigInterface
     */
    private $defaultConfig;
    /**
     * @var ToolInfoInterface
     */
    private $toolInfo;
    public function __construct(\PhpCsFixer\ToolInfoInterface $toolInfo)
    {
        parent::__construct();
        $this->defaultConfig = new \PhpCsFixer\Config();
        $this->errorsManager = new \PhpCsFixer\Error\ErrorsManager();
        $this->eventDispatcher = new \ECSPrefix20210715\Symfony\Component\EventDispatcher\EventDispatcher();
        $this->stopwatch = new \ECSPrefix20210715\Symfony\Component\Stopwatch\Stopwatch();
        $this->toolInfo = $toolInfo;
    }
    /**
     * {@inheritdoc}
     *
     * Override here to only generate the help copy when used.
     */
    public function getHelp() : string
    {
        return <<<'EOF'
The <info>%command.name%</info> command tries to fix as much coding standards
problems as possible on a given file or files in a given directory and its subdirectories:

    <info>$ php %command.full_name% /path/to/dir</info>
    <info>$ php %command.full_name% /path/to/file</info>

By default <comment>--path-mode</comment> is set to `override`, which means, that if you specify the path to a file or a directory via
command arguments, then the paths provided to a `Finder` in config file will be ignored. You can use <comment>--path-mode=intersection</comment>
to merge paths from the config file and from the argument:

    <info>$ php %command.full_name% --path-mode=intersection /path/to/dir</info>

The <comment>--format</comment> option for the output format. Supported formats are `txt` (default one), `json`, `xml`, `checkstyle`, `junit` and `gitlab`.

NOTE: the output for the following formats are generated in accordance with schemas

* `checkstyle` follows the common `"checkstyle" XML schema </doc/schemas/fix/checkstyle.xsd>`_
* `json` follows the `own JSON schema </doc/schemas/fix/schema.json>`_
* `junit` follows the `JUnit XML schema from Jenkins </doc/schemas/fix/junit-10.xsd>`_
* `xml` follows the `own XML schema </doc/schemas/fix/xml.xsd>`_

The <comment>--quiet</comment> Do not output any message.

The <comment>--verbose</comment> option will show the applied rules. When using the `txt` format it will also display progress notifications.

NOTE: if there is an error like "errors reported during linting after fixing", you can use this to be even more verbose for debugging purpose

* `-v`: verbose
* `-vv`: very verbose
* `-vvv`: debug

The <comment>--rules</comment> option limits the rules to apply to the
project:

    <info>$ php %command.full_name% /path/to/project --rules=@PSR12</info>

By default the PSR-12 rules are used.

The <comment>--rules</comment> option lets you choose the exact rules to
apply (the rule names must be separated by a comma):

    <info>$ php %command.full_name% /path/to/dir --rules=line_ending,full_opening_tag,indentation_type</info>

You can also exclude the rules you don't want by placing a dash in front of the rule name, if this is more convenient,
using <comment>-name_of_fixer</comment>:

    <info>$ php %command.full_name% /path/to/dir --rules=-full_opening_tag,-indentation_type</info>

When using combinations of exact and exclude rules, applying exact rules along with above excluded results:

    <info>$ php %command.full_name% /path/to/project --rules=@Symfony,-@PSR1,-blank_line_before_statement,strict_comparison</info>

Complete configuration for rules can be supplied using a `json` formatted string.

    <info>$ php %command.full_name% /path/to/project --rules='{"concat_space": {"spacing": "none"}}'</info>

The <comment>--dry-run</comment> flag will run the fixer without making changes to your files.

The <comment>--diff</comment> flag can be used to let the fixer output all the changes it makes.

The <comment>--allow-risky</comment> option (pass `yes` or `no`) allows you to set whether risky rules may run. Default value is taken from config file.
A rule is considered risky if it could change code behaviour. By default no risky rules are run.

The <comment>--stop-on-violation</comment> flag stops the execution upon first file that needs to be fixed.

The <comment>--show-progress</comment> option allows you to choose the way process progress is rendered:

* <comment>none</comment>: disables progress output;
* <comment>dots</comment>: multiline progress output with number of files and percentage on each line.

If the option is not provided, it defaults to <comment>dots</comment> unless a config file that disables output is used, in which case it defaults to <comment>none</comment>. This option has no effect if the verbosity of the command is less than <comment>verbose</comment>.

    <info>$ php %command.full_name% --verbose --show-progress=dots</info>

By using <command>--using-cache</command> option with `yes` or `no` you can set if the caching
mechanism should be used.

The command can also read from standard input, in which case it won't
automatically fix anything:

    <info>$ cat foo.php | php %command.full_name% --diff -</info>

Finally, if you don't need BC kept on CLI level, you might use `PHP_CS_FIXER_FUTURE_MODE` to start using options that
would be default in next MAJOR release and to forbid using deprecated configuration:

    <info>$ PHP_CS_FIXER_FUTURE_MODE=1 php %command.full_name% -v --diff</info>

Exit code
---------

Exit code of the fix command is built using following bit flags:

*  0 - OK.
*  1 - General error (or PHP minimal requirement not matched).
*  4 - Some files have invalid syntax (only in dry-run mode).
*  8 - Some files need fixing (only in dry-run mode).
* 16 - Configuration error of the application.
* 32 - Configuration error of a Fixer.
* 64 - Exception raised within the application.

EOF;
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function configure()
    {
        $this->setDefinition([new \ECSPrefix20210715\Symfony\Component\Console\Input\InputArgument('path', \ECSPrefix20210715\Symfony\Component\Console\Input\InputArgument::IS_ARRAY, 'The path.'), new \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption('path-mode', '', \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Specify path mode (can be override or intersection).', \PhpCsFixer\Console\ConfigurationResolver::PATH_MODE_OVERRIDE), new \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption('allow-risky', '', \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Are risky fixers allowed (can be yes or no).'), new \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption('config', '', \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'The path to a .php-cs-fixer.php file.'), new \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption('dry-run', '', \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Only shows which files would have been modified.'), new \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption('rules', '', \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'The rules.'), new \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption('using-cache', '', \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Does cache should be used (can be yes or no).'), new \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption('cache-file', '', \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'The path to the cache file.'), new \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption('diff', '', \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Also produce diff for each file.'), new \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption('format', '', \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'To output results in other formats.'), new \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption('stop-on-violation', '', \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Stop execution on first violation.'), new \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption('show-progress', '', \ECSPrefix20210715\Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Type of progress indicator (none, dots).')])->setDescription('Fixes a directory or a file.');
    }
    /**
     * {@inheritdoc}
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute($input, $output) : int
    {
        $verbosity = $output->getVerbosity();
        $passedConfig = $input->getOption('config');
        $passedRules = $input->getOption('rules');
        if (null !== $passedConfig && null !== $passedRules) {
            throw new \PhpCsFixer\ConfigurationException\InvalidConfigurationException('Passing both `--config` and `--rules` options is not allowed.');
        }
        $resolver = new \PhpCsFixer\Console\ConfigurationResolver($this->defaultConfig, ['allow-risky' => $input->getOption('allow-risky'), 'config' => $passedConfig, 'dry-run' => $input->getOption('dry-run'), 'rules' => $passedRules, 'path' => $input->getArgument('path'), 'path-mode' => $input->getOption('path-mode'), 'using-cache' => $input->getOption('using-cache'), 'cache-file' => $input->getOption('cache-file'), 'format' => $input->getOption('format'), 'diff' => $input->getOption('diff'), 'stop-on-violation' => $input->getOption('stop-on-violation'), 'verbosity' => $verbosity, 'show-progress' => $input->getOption('show-progress')], \getcwd(), $this->toolInfo);
        $reporter = $resolver->getReporter();
        $stdErr = $output instanceof \ECSPrefix20210715\Symfony\Component\Console\Output\ConsoleOutputInterface ? $output->getErrorOutput() : ('txt' === $reporter->getFormat() ? $output : null);
        if (null !== $stdErr) {
            if (\ECSPrefix20210715\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_VERBOSE <= $verbosity) {
                $stdErr->writeln($this->getApplication()->getLongVersion());
                $stdErr->writeln(\sprintf('Runtime: <info>PHP %s</info>', \PHP_VERSION));
            }
            $configFile = $resolver->getConfigFile();
            $stdErr->writeln(\sprintf('Loaded config <comment>%s</comment>%s.', $resolver->getConfig()->getName(), null === $configFile ? '' : ' from "' . $configFile . '"'));
            if ($resolver->getUsingCache()) {
                $cacheFile = $resolver->getCacheFile();
                if (\is_file($cacheFile)) {
                    $stdErr->writeln(\sprintf('Using cache file "%s".', $cacheFile));
                }
            }
        }
        $progressType = $resolver->getProgress();
        $finder = $resolver->getFinder();
        if (null !== $stdErr && $resolver->configFinderIsOverridden()) {
            $stdErr->writeln(\sprintf($stdErr->isDecorated() ? '<bg=yellow;fg=black;>%s</>' : '%s', 'Paths from configuration file have been overridden by paths provided as command arguments.'));
        }
        if ('none' === $progressType || null === $stdErr) {
            $progressOutput = new \PhpCsFixer\Console\Output\NullOutput();
        } else {
            $finder = new \ArrayIterator(\iterator_to_array($finder));
            $progressOutput = new \PhpCsFixer\Console\Output\ProcessOutput($stdErr, $this->eventDispatcher, (new \ECSPrefix20210715\Symfony\Component\Console\Terminal())->getWidth(), \count($finder));
        }
        $runner = new \PhpCsFixer\Runner\Runner($finder, $resolver->getFixers(), $resolver->getDiffer(), 'none' !== $progressType ? $this->eventDispatcher : null, $this->errorsManager, $resolver->getLinter(), $resolver->isDryRun(), $resolver->getCacheManager(), $resolver->getDirectory(), $resolver->shouldStopOnViolation());
        $this->stopwatch->start('fixFiles');
        $changed = $runner->fix();
        $this->stopwatch->stop('fixFiles');
        $progressOutput->printLegend();
        $fixEvent = $this->stopwatch->getEvent('fixFiles');
        $reportSummary = new \PhpCsFixer\Console\Report\FixReport\ReportSummary($changed, $fixEvent->getDuration(), $fixEvent->getMemory(), \ECSPrefix20210715\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_VERBOSE <= $verbosity, $resolver->isDryRun(), $output->isDecorated());
        $output->isDecorated() ? $output->write($reporter->generate($reportSummary)) : $output->write($reporter->generate($reportSummary), \false, \ECSPrefix20210715\Symfony\Component\Console\Output\OutputInterface::OUTPUT_RAW);
        $invalidErrors = $this->errorsManager->getInvalidErrors();
        $exceptionErrors = $this->errorsManager->getExceptionErrors();
        $lintErrors = $this->errorsManager->getLintErrors();
        if (null !== $stdErr) {
            $errorOutput = new \PhpCsFixer\Console\Output\ErrorOutput($stdErr);
            if (\count($invalidErrors) > 0) {
                $errorOutput->listErrors('linting before fixing', $invalidErrors);
            }
            if (\count($exceptionErrors) > 0) {
                $errorOutput->listErrors('fixing', $exceptionErrors);
            }
            if (\count($lintErrors) > 0) {
                $errorOutput->listErrors('linting after fixing', $lintErrors);
            }
        }
        $exitStatusCalculator = new \PhpCsFixer\Console\Command\FixCommandExitStatusCalculator();
        return $exitStatusCalculator->calculate($resolver->isDryRun(), \count($changed) > 0, \count($invalidErrors) > 0, \count($exceptionErrors) > 0, \count($lintErrors) > 0);
    }
}
