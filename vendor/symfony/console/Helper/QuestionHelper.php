<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211119\Symfony\Component\Console\Helper;

use ECSPrefix20211119\Symfony\Component\Console\Cursor;
use ECSPrefix20211119\Symfony\Component\Console\Exception\MissingInputException;
use ECSPrefix20211119\Symfony\Component\Console\Exception\RuntimeException;
use ECSPrefix20211119\Symfony\Component\Console\Formatter\OutputFormatter;
use ECSPrefix20211119\Symfony\Component\Console\Formatter\OutputFormatterStyle;
use ECSPrefix20211119\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20211119\Symfony\Component\Console\Input\StreamableInputInterface;
use ECSPrefix20211119\Symfony\Component\Console\Output\ConsoleOutputInterface;
use ECSPrefix20211119\Symfony\Component\Console\Output\ConsoleSectionOutput;
use ECSPrefix20211119\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20211119\Symfony\Component\Console\Question\ChoiceQuestion;
use ECSPrefix20211119\Symfony\Component\Console\Question\Question;
use ECSPrefix20211119\Symfony\Component\Console\Terminal;
use function ECSPrefix20211119\Symfony\Component\String\s;
/**
 * The QuestionHelper class provides helpers to interact with the user.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class QuestionHelper extends \ECSPrefix20211119\Symfony\Component\Console\Helper\Helper
{
    private $inputStream;
    private static $shell;
    private static $stty = \true;
    private static $stdinIsInteractive;
    /**
     * Asks a question to the user.
     *
     * @return mixed The user answer
     *
     * @throws RuntimeException If there is no data to read in the input stream
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Symfony\Component\Console\Question\Question $question
     */
    public function ask($input, $output, $question)
    {
        if ($output instanceof \ECSPrefix20211119\Symfony\Component\Console\Output\ConsoleOutputInterface) {
            $output = $output->getErrorOutput();
        }
        if (!$input->isInteractive()) {
            return $this->getDefaultAnswer($question);
        }
        if ($input instanceof \ECSPrefix20211119\Symfony\Component\Console\Input\StreamableInputInterface && ($stream = $input->getStream())) {
            $this->inputStream = $stream;
        }
        try {
            if (!$question->getValidator()) {
                return $this->doAsk($output, $question);
            }
            $interviewer = function () use($output, $question) {
                return $this->doAsk($output, $question);
            };
            return $this->validateAttempts($interviewer, $output, $question);
        } catch (\ECSPrefix20211119\Symfony\Component\Console\Exception\MissingInputException $exception) {
            $input->setInteractive(\false);
            if (null === ($fallbackOutput = $this->getDefaultAnswer($question))) {
                throw $exception;
            }
            return $fallbackOutput;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'question';
    }
    /**
     * Prevents usage of stty.
     */
    public static function disableStty()
    {
        self::$stty = \false;
    }
    /**
     * Asks the question to the user.
     *
     * @return mixed
     *
     * @throws RuntimeException In case the fallback is deactivated and the response cannot be hidden
     */
    private function doAsk(\ECSPrefix20211119\Symfony\Component\Console\Output\OutputInterface $output, \ECSPrefix20211119\Symfony\Component\Console\Question\Question $question)
    {
        $this->writePrompt($output, $question);
        $inputStream = $this->inputStream ?: \STDIN;
        $autocomplete = $question->getAutocompleterCallback();
        if (null === $autocomplete || !self::$stty || !\ECSPrefix20211119\Symfony\Component\Console\Terminal::hasSttyAvailable()) {
            $ret = \false;
            if ($question->isHidden()) {
                try {
                    $hiddenResponse = $this->getHiddenResponse($output, $inputStream, $question->isTrimmable());
                    $ret = $question->isTrimmable() ? \trim($hiddenResponse) : $hiddenResponse;
                } catch (\ECSPrefix20211119\Symfony\Component\Console\Exception\RuntimeException $e) {
                    if (!$question->isHiddenFallback()) {
                        throw $e;
                    }
                }
            }
            if (\false === $ret) {
                $ret = $this->readInput($inputStream, $question);
                if (\false === $ret) {
                    throw new \ECSPrefix20211119\Symfony\Component\Console\Exception\MissingInputException('Aborted.');
                }
                if ($question->isTrimmable()) {
                    $ret = \trim($ret);
                }
            }
        } else {
            $autocomplete = $this->autocomplete($output, $question, $inputStream, $autocomplete);
            $ret = $question->isTrimmable() ? \trim($autocomplete) : $autocomplete;
        }
        if ($output instanceof \ECSPrefix20211119\Symfony\Component\Console\Output\ConsoleSectionOutput) {
            $output->addContent($ret);
        }
        $ret = \strlen($ret) > 0 ? $ret : $question->getDefault();
        if ($normalizer = $question->getNormalizer()) {
            return $normalizer($ret);
        }
        return $ret;
    }
    /**
     * @return mixed
     */
    private function getDefaultAnswer(\ECSPrefix20211119\Symfony\Component\Console\Question\Question $question)
    {
        $default = $question->getDefault();
        if (null === $default) {
            return $default;
        }
        if ($validator = $question->getValidator()) {
            return \call_user_func($question->getValidator(), $default);
        } elseif ($question instanceof \ECSPrefix20211119\Symfony\Component\Console\Question\ChoiceQuestion) {
            $choices = $question->getChoices();
            if (!$question->isMultiselect()) {
                return $choices[$default] ?? $default;
            }
            $default = \explode(',', $default);
            foreach ($default as $k => $v) {
                $v = $question->isTrimmable() ? \trim($v) : $v;
                $default[$k] = $choices[$v] ?? $v;
            }
        }
        return $default;
    }
    /**
     * Outputs the question prompt.
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Symfony\Component\Console\Question\Question $question
     */
    protected function writePrompt($output, $question)
    {
        $message = $question->getQuestion();
        if ($question instanceof \ECSPrefix20211119\Symfony\Component\Console\Question\ChoiceQuestion) {
            $output->writeln(\array_merge([$question->getQuestion()], $this->formatChoiceQuestionChoices($question, 'info')));
            $message = $question->getPrompt();
        }
        $output->write($message);
    }
    /**
     * @return string[]
     * @param \Symfony\Component\Console\Question\ChoiceQuestion $question
     * @param string $tag
     */
    protected function formatChoiceQuestionChoices($question, $tag)
    {
        $messages = [];
        $maxWidth = \max(\array_map([__CLASS__, 'width'], \array_keys($choices = $question->getChoices())));
        foreach ($choices as $key => $value) {
            $padding = \str_repeat(' ', $maxWidth - self::width($key));
            $messages[] = \sprintf("  [<{$tag}>%s{$padding}</{$tag}>] %s", $key, $value);
        }
        return $messages;
    }
    /**
     * Outputs an error message.
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Exception $error
     */
    protected function writeError($output, $error)
    {
        if (null !== $this->getHelperSet() && $this->getHelperSet()->has('formatter')) {
            $message = $this->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error');
        } else {
            $message = '<error>' . $error->getMessage() . '</error>';
        }
        $output->writeln($message);
    }
    /**
     * Autocompletes a question.
     *
     * @param resource $inputStream
     */
    private function autocomplete(\ECSPrefix20211119\Symfony\Component\Console\Output\OutputInterface $output, \ECSPrefix20211119\Symfony\Component\Console\Question\Question $question, $inputStream, callable $autocomplete) : string
    {
        $cursor = new \ECSPrefix20211119\Symfony\Component\Console\Cursor($output, $inputStream);
        $fullChoice = '';
        $ret = '';
        $i = 0;
        $ofs = -1;
        $matches = $autocomplete($ret);
        $numMatches = \count($matches);
        $sttyMode = \shell_exec('stty -g');
        // Disable icanon (so we can fread each keypress) and echo (we'll do echoing here instead)
        \shell_exec('stty -icanon -echo');
        // Add highlighted text style
        $output->getFormatter()->setStyle('hl', new \ECSPrefix20211119\Symfony\Component\Console\Formatter\OutputFormatterStyle('black', 'white'));
        // Read a keypress
        while (!\feof($inputStream)) {
            $c = \fread($inputStream, 1);
            // as opposed to fgets(), fread() returns an empty string when the stream content is empty, not false.
            if (\false === $c || '' === $ret && '' === $c && null === $question->getDefault()) {
                \shell_exec(\sprintf('stty %s', $sttyMode));
                throw new \ECSPrefix20211119\Symfony\Component\Console\Exception\MissingInputException('Aborted.');
            } elseif ("" === $c) {
                // Backspace Character
                if (0 === $numMatches && 0 !== $i) {
                    --$i;
                    $cursor->moveLeft(\ECSPrefix20211119\Symfony\Component\String\s($fullChoice)->slice(-1)->width(\false));
                    $fullChoice = self::substr($fullChoice, 0, $i);
                }
                if (0 === $i) {
                    $ofs = -1;
                    $matches = $autocomplete($ret);
                    $numMatches = \count($matches);
                } else {
                    $numMatches = 0;
                }
                // Pop the last character off the end of our string
                $ret = self::substr($ret, 0, $i);
            } elseif ("\33" === $c) {
                // Did we read an escape sequence?
                $c .= \fread($inputStream, 2);
                // A = Up Arrow. B = Down Arrow
                if (isset($c[2]) && ('A' === $c[2] || 'B' === $c[2])) {
                    if ('A' === $c[2] && -1 === $ofs) {
                        $ofs = 0;
                    }
                    if (0 === $numMatches) {
                        continue;
                    }
                    $ofs += 'A' === $c[2] ? -1 : 1;
                    $ofs = ($numMatches + $ofs) % $numMatches;
                }
            } elseif (\ord($c) < 32) {
                if ("\t" === $c || "\n" === $c) {
                    if ($numMatches > 0 && -1 !== $ofs) {
                        $ret = (string) $matches[$ofs];
                        // Echo out remaining chars for current match
                        $remainingCharacters = \substr($ret, \strlen(\trim($this->mostRecentlyEnteredValue($fullChoice))));
                        $output->write($remainingCharacters);
                        $fullChoice .= $remainingCharacters;
                        $i = \false === ($encoding = \mb_detect_encoding($fullChoice, null, \true)) ? \strlen($fullChoice) : \mb_strlen($fullChoice, $encoding);
                        $matches = \array_filter($autocomplete($ret), function ($match) use($ret) {
                            return '' === $ret || \strncmp($match, $ret, \strlen($ret)) === 0;
                        });
                        $numMatches = \count($matches);
                        $ofs = -1;
                    }
                    if ("\n" === $c) {
                        $output->write($c);
                        break;
                    }
                    $numMatches = 0;
                }
                continue;
            } else {
                if ("�" <= $c) {
                    $c .= \fread($inputStream, ["�" => 1, "�" => 1, "�" => 2, "�" => 3][$c & "�"]);
                }
                $output->write($c);
                $ret .= $c;
                $fullChoice .= $c;
                ++$i;
                $tempRet = $ret;
                if ($question instanceof \ECSPrefix20211119\Symfony\Component\Console\Question\ChoiceQuestion && $question->isMultiselect()) {
                    $tempRet = $this->mostRecentlyEnteredValue($fullChoice);
                }
                $numMatches = 0;
                $ofs = 0;
                foreach ($autocomplete($ret) as $value) {
                    // If typed characters match the beginning chunk of value (e.g. [AcmeDe]moBundle)
                    if (\strncmp($value, $tempRet, \strlen($tempRet)) === 0) {
                        $matches[$numMatches++] = $value;
                    }
                }
            }
            $cursor->clearLineAfter();
            if ($numMatches > 0 && -1 !== $ofs) {
                $cursor->savePosition();
                // Write highlighted text, complete the partially entered response
                $charactersEntered = \strlen(\trim($this->mostRecentlyEnteredValue($fullChoice)));
                $output->write('<hl>' . \ECSPrefix20211119\Symfony\Component\Console\Formatter\OutputFormatter::escapeTrailingBackslash(\substr($matches[$ofs], $charactersEntered)) . '</hl>');
                $cursor->restorePosition();
            }
        }
        // Reset stty so it behaves normally again
        \shell_exec(\sprintf('stty %s', $sttyMode));
        return $fullChoice;
    }
    private function mostRecentlyEnteredValue(string $entered) : string
    {
        // Determine the most recent value that the user entered
        if (\strpos($entered, ',') === \false) {
            return $entered;
        }
        $choices = \explode(',', $entered);
        if ('' !== ($lastChoice = \trim($choices[\count($choices) - 1]))) {
            return $lastChoice;
        }
        return $entered;
    }
    /**
     * Gets a hidden response from user.
     *
     * @param resource $inputStream The handler resource
     * @param bool     $trimmable   Is the answer trimmable
     *
     * @throws RuntimeException In case the fallback is deactivated and the response cannot be hidden
     */
    private function getHiddenResponse(\ECSPrefix20211119\Symfony\Component\Console\Output\OutputInterface $output, $inputStream, bool $trimmable = \true) : string
    {
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $exe = __DIR__ . '/../Resources/bin/hiddeninput.exe';
            // handle code running from a phar
            if ('phar:' === \substr(__FILE__, 0, 5)) {
                $tmpExe = \sys_get_temp_dir() . '/hiddeninput.exe';
                \copy($exe, $tmpExe);
                $exe = $tmpExe;
            }
            $sExec = \shell_exec('"' . $exe . '"');
            $value = $trimmable ? \rtrim($sExec) : $sExec;
            $output->writeln('');
            if (isset($tmpExe)) {
                \unlink($tmpExe);
            }
            return $value;
        }
        if (self::$stty && \ECSPrefix20211119\Symfony\Component\Console\Terminal::hasSttyAvailable()) {
            $sttyMode = \shell_exec('stty -g');
            \shell_exec('stty -echo');
        } elseif ($this->isInteractiveInput($inputStream)) {
            throw new \ECSPrefix20211119\Symfony\Component\Console\Exception\RuntimeException('Unable to hide the response.');
        }
        $value = \fgets($inputStream, 4096);
        if (self::$stty && \ECSPrefix20211119\Symfony\Component\Console\Terminal::hasSttyAvailable()) {
            \shell_exec(\sprintf('stty %s', $sttyMode));
        }
        if (\false === $value) {
            throw new \ECSPrefix20211119\Symfony\Component\Console\Exception\MissingInputException('Aborted.');
        }
        if ($trimmable) {
            $value = \trim($value);
        }
        $output->writeln('');
        return $value;
    }
    /**
     * Validates an attempt.
     *
     * @param callable $interviewer A callable that will ask for a question and return the result
     *
     * @return mixed The validated response
     *
     * @throws \Exception In case the max number of attempts has been reached and no valid response has been given
     */
    private function validateAttempts(callable $interviewer, \ECSPrefix20211119\Symfony\Component\Console\Output\OutputInterface $output, \ECSPrefix20211119\Symfony\Component\Console\Question\Question $question)
    {
        $error = null;
        $attempts = $question->getMaxAttempts();
        while (null === $attempts || $attempts--) {
            if (null !== $error) {
                $this->writeError($output, $error);
            }
            try {
                return $question->getValidator()($interviewer());
            } catch (\ECSPrefix20211119\Symfony\Component\Console\Exception\RuntimeException $e) {
                throw $e;
            } catch (\Exception $error) {
            }
        }
        throw $error;
    }
    private function isInteractiveInput($inputStream) : bool
    {
        if ('php://stdin' !== (\stream_get_meta_data($inputStream)['uri'] ?? null)) {
            return \false;
        }
        if (null !== self::$stdinIsInteractive) {
            return self::$stdinIsInteractive;
        }
        if (\function_exists('stream_isatty')) {
            return self::$stdinIsInteractive = \stream_isatty(\fopen('php://stdin', 'r'));
        }
        if (\function_exists('posix_isatty')) {
            return self::$stdinIsInteractive = \posix_isatty(\fopen('php://stdin', 'r'));
        }
        if (!\function_exists('exec')) {
            return self::$stdinIsInteractive = \true;
        }
        \exec('stty 2> /dev/null', $output, $status);
        return self::$stdinIsInteractive = 1 !== $status;
    }
    /**
     * Reads one or more lines of input and returns what is read.
     *
     * @param resource $inputStream The handler resource
     * @param Question $question    The question being asked
     *
     * @return string|false The input received, false in case input could not be read
     */
    private function readInput($inputStream, \ECSPrefix20211119\Symfony\Component\Console\Question\Question $question)
    {
        if (!$question->isMultiline()) {
            $cp = $this->setIOCodepage();
            $ret = \fgets($inputStream, 4096);
            return $this->resetIOCodepage($cp, $ret);
        }
        $multiLineStreamReader = $this->cloneInputStream($inputStream);
        if (null === $multiLineStreamReader) {
            return \false;
        }
        $ret = '';
        $cp = $this->setIOCodepage();
        while (\false !== ($char = \fgetc($multiLineStreamReader))) {
            if (\PHP_EOL === "{$ret}{$char}") {
                break;
            }
            $ret .= $char;
        }
        return $this->resetIOCodepage($cp, $ret);
    }
    /**
     * Sets console I/O to the host code page.
     *
     * @return int Previous code page in IBM/EBCDIC format
     */
    private function setIOCodepage() : int
    {
        if (\function_exists('sapi_windows_cp_set')) {
            $cp = \sapi_windows_cp_get();
            \sapi_windows_cp_set(\sapi_windows_cp_get('oem'));
            return $cp;
        }
        return 0;
    }
    /**
     * Sets console I/O to the specified code page and converts the user input.
     *
     * @param string|false $input
     *
     * @return string|false
     */
    private function resetIOCodepage(int $cp, $input)
    {
        if (0 !== $cp) {
            \sapi_windows_cp_set($cp);
            if (\false !== $input && '' !== $input) {
                $input = \sapi_windows_cp_conv(\sapi_windows_cp_get('oem'), $cp, $input);
            }
        }
        return $input;
    }
    /**
     * Clones an input stream in order to act on one instance of the same
     * stream without affecting the other instance.
     *
     * @param resource $inputStream The handler resource
     *
     * @return resource|null The cloned resource, null in case it could not be cloned
     */
    private function cloneInputStream($inputStream)
    {
        $streamMetaData = \stream_get_meta_data($inputStream);
        $seekable = $streamMetaData['seekable'] ?? \false;
        $mode = $streamMetaData['mode'] ?? 'rb';
        $uri = $streamMetaData['uri'] ?? null;
        if (null === $uri) {
            return null;
        }
        $cloneStream = \fopen($uri, $mode);
        // For seekable and writable streams, add all the same data to the
        // cloned stream and then seek to the same offset.
        if (\true === $seekable && !\in_array($mode, ['r', 'rb', 'rt'])) {
            $offset = \ftell($inputStream);
            \rewind($inputStream);
            \stream_copy_to_stream($inputStream, $cloneStream);
            \fseek($inputStream, $offset);
            \fseek($cloneStream, $offset);
        }
        return $cloneStream;
    }
}
