<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202408\Symfony\Component\Console\Tester;

use ECSPrefix202408\PHPUnit\Framework\Assert;
use ECSPrefix202408\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix202408\Symfony\Component\Console\Output\ConsoleOutput;
use ECSPrefix202408\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix202408\Symfony\Component\Console\Output\StreamOutput;
use ECSPrefix202408\Symfony\Component\Console\Tester\Constraint\CommandIsSuccessful;
/**
 * @author Amrouche Hamza <hamza.simperfit@gmail.com>
 */
trait TesterTrait
{
    /**
     * @var \Symfony\Component\Console\Output\StreamOutput
     */
    private $output;
    /**
     * @var mixed[]
     */
    private $inputs = [];
    /**
     * @var bool
     */
    private $captureStreamsIndependently = \false;
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;
    /**
     * @var int
     */
    private $statusCode;
    /**
     * Gets the display returned by the last execution of the command or application.
     *
     * @throws \RuntimeException If it's called before the execute method
     */
    public function getDisplay(bool $normalize = \false) : string
    {
        if (!isset($this->output)) {
            throw new \RuntimeException('Output not initialized, did you execute the command before requesting the display?');
        }
        \rewind($this->output->getStream());
        $display = \stream_get_contents($this->output->getStream());
        if ($normalize) {
            $display = \str_replace(\PHP_EOL, "\n", $display);
        }
        return $display;
    }
    /**
     * Gets the output written to STDERR by the application.
     *
     * @param bool $normalize Whether to normalize end of lines to \n or not
     */
    public function getErrorOutput(bool $normalize = \false) : string
    {
        if (!$this->captureStreamsIndependently) {
            throw new \LogicException('The error output is not available when the tester is run without "capture_stderr_separately" option set.');
        }
        \rewind($this->output->getErrorOutput()->getStream());
        $display = \stream_get_contents($this->output->getErrorOutput()->getStream());
        if ($normalize) {
            $display = \str_replace(\PHP_EOL, "\n", $display);
        }
        return $display;
    }
    /**
     * Gets the input instance used by the last execution of the command or application.
     */
    public function getInput() : InputInterface
    {
        return $this->input;
    }
    /**
     * Gets the output instance used by the last execution of the command or application.
     */
    public function getOutput() : OutputInterface
    {
        return $this->output;
    }
    /**
     * Gets the status code returned by the last execution of the command or application.
     *
     * @throws \RuntimeException If it's called before the execute method
     */
    public function getStatusCode() : int
    {
        if (!isset($this->statusCode)) {
            throw new \RuntimeException('Status code not initialized, did you execute the command before requesting the status code?');
        }
        return $this->statusCode;
    }
    public function assertCommandIsSuccessful(string $message = '') : void
    {
        Assert::assertThat($this->statusCode, new CommandIsSuccessful(), $message);
    }
    /**
     * Sets the user inputs.
     *
     * @param array $inputs An array of strings representing each input
     *                      passed to the command input stream
     *
     * @return $this
     */
    public function setInputs(array $inputs)
    {
        $this->inputs = $inputs;
        return $this;
    }
    /**
     * Initializes the output property.
     *
     * Available options:
     *
     *  * decorated:                 Sets the output decorated flag
     *  * verbosity:                 Sets the output verbosity flag
     *  * capture_stderr_separately: Make output of stdOut and stdErr separately available
     */
    private function initOutput(array $options) : void
    {
        $this->captureStreamsIndependently = $options['capture_stderr_separately'] ?? \false;
        if (!$this->captureStreamsIndependently) {
            $this->output = new StreamOutput(\fopen('php://memory', 'w', \false));
            if (isset($options['decorated'])) {
                $this->output->setDecorated($options['decorated']);
            }
            if (isset($options['verbosity'])) {
                $this->output->setVerbosity($options['verbosity']);
            }
        } else {
            $this->output = new ConsoleOutput($options['verbosity'] ?? ConsoleOutput::VERBOSITY_NORMAL, $options['decorated'] ?? null);
            $errorOutput = new StreamOutput(\fopen('php://memory', 'w', \false));
            $errorOutput->setFormatter($this->output->getFormatter());
            $errorOutput->setVerbosity($this->output->getVerbosity());
            $errorOutput->setDecorated($this->output->isDecorated());
            $reflectedOutput = new \ReflectionObject($this->output);
            $strErrProperty = $reflectedOutput->getProperty('stderr');
            $strErrProperty->setValue($this->output, $errorOutput);
            $reflectedParent = $reflectedOutput->getParentClass();
            $streamProperty = $reflectedParent->getProperty('stream');
            $streamProperty->setValue($this->output, \fopen('php://memory', 'w', \false));
        }
    }
    /**
     * @return resource
     */
    private static function createStream(array $inputs)
    {
        $stream = \fopen('php://memory', 'r+', \false);
        foreach ($inputs as $input) {
            \fwrite($stream, $input . \PHP_EOL);
        }
        \rewind($stream);
        return $stream;
    }
}
