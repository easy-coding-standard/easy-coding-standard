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
namespace PhpCsFixer\Runner\Parallel;

use PhpCsFixer\Runner\RunnerConfig;
use ECSPrefix202408\React\EventLoop\LoopInterface;
use ECSPrefix202408\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix202408\Symfony\Component\Process\PhpExecutableFinder;
/**
 * @author Greg Korba <greg@codito.dev>
 *
 * @internal
 */
final class ProcessFactory
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;
    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }
    public function create(LoopInterface $loop, RunnerConfig $runnerConfig, \PhpCsFixer\Runner\Parallel\ProcessIdentifier $identifier, int $serverPort) : \PhpCsFixer\Runner\Parallel\Process
    {
        $commandArgs = $this->getCommandArgs($serverPort, $identifier, $runnerConfig);
        return new \PhpCsFixer\Runner\Parallel\Process(\implode(' ', $commandArgs), $loop, $runnerConfig->getParallelConfig()->getProcessTimeout());
    }
    /**
     * @private
     *
     * @return list<string>
     */
    public function getCommandArgs(int $serverPort, \PhpCsFixer\Runner\Parallel\ProcessIdentifier $identifier, RunnerConfig $runnerConfig) : array
    {
        $phpBinary = (new PhpExecutableFinder())->find(\false);
        if (\false === $phpBinary) {
            throw new \PhpCsFixer\Runner\Parallel\ParallelisationException('Cannot find PHP executable.');
        }
        $mainScript = \realpath(__DIR__ . '/../../../php-cs-fixer');
        if (\false === $mainScript && isset($_SERVER['argv'][0]) && \strpos($_SERVER['argv'][0], 'php-cs-fixer') !== \false) {
            $mainScript = $_SERVER['argv'][0];
        }
        if (!\is_file($mainScript)) {
            throw new \PhpCsFixer\Runner\Parallel\ParallelisationException('Cannot determine Fixer executable.');
        }
        $commandArgs = [$phpBinary, \escapeshellarg($mainScript), 'worker', '--port', (string) $serverPort, '--identifier', \escapeshellarg($identifier->toString())];
        if ($runnerConfig->isDryRun()) {
            $commandArgs[] = '--dry-run';
        }
        if (\filter_var($this->input->getOption('diff'), \FILTER_VALIDATE_BOOLEAN)) {
            $commandArgs[] = '--diff';
        }
        if (\filter_var($this->input->getOption('stop-on-violation'), \FILTER_VALIDATE_BOOLEAN)) {
            $commandArgs[] = '--stop-on-violation';
        }
        foreach (['allow-risky', 'config', 'rules', 'using-cache', 'cache-file'] as $option) {
            $optionValue = $this->input->getOption($option);
            if (null !== $optionValue) {
                $commandArgs[] = "--{$option}";
                $commandArgs[] = \escapeshellarg($optionValue);
            }
        }
        return $commandArgs;
    }
}
