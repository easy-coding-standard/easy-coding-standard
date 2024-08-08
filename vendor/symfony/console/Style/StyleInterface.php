<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202408\Symfony\Component\Console\Style;

/**
 * Output style helpers.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface StyleInterface
{
    /**
     * Formats a command title.
     *
     * @return void
     */
    public function title(string $message);
    /**
     * Formats a section title.
     *
     * @return void
     */
    public function section(string $message);
    /**
     * Formats a list.
     *
     * @return void
     */
    public function listing(array $elements);
    /**
     * Formats informational text.
     *
     * @return void
     * @param string|mixed[] $message
     */
    public function text($message);
    /**
     * Formats a success result bar.
     *
     * @return void
     * @param string|mixed[] $message
     */
    public function success($message);
    /**
     * Formats an error result bar.
     *
     * @return void
     * @param string|mixed[] $message
     */
    public function error($message);
    /**
     * Formats an warning result bar.
     *
     * @return void
     * @param string|mixed[] $message
     */
    public function warning($message);
    /**
     * Formats a note admonition.
     *
     * @return void
     * @param string|mixed[] $message
     */
    public function note($message);
    /**
     * Formats a caution admonition.
     *
     * @return void
     * @param string|mixed[] $message
     */
    public function caution($message);
    /**
     * Formats a table.
     *
     * @return void
     */
    public function table(array $headers, array $rows);
    /**
     * Asks a question.
     * @return mixed
     */
    public function ask(string $question, ?string $default = null, ?callable $validator = null);
    /**
     * Asks a question with the user input hidden.
     * @return mixed
     */
    public function askHidden(string $question, ?callable $validator = null);
    /**
     * Asks for confirmation.
     */
    public function confirm(string $question, bool $default = \true) : bool;
    /**
     * Asks a choice question.
     * @param mixed $default
     * @return mixed
     */
    public function choice(string $question, array $choices, $default = null);
    /**
     * Add newline(s).
     *
     * @return void
     */
    public function newLine(int $count = 1);
    /**
     * Starts the progress output.
     *
     * @return void
     */
    public function progressStart(int $max = 0);
    /**
     * Advances the progress output X steps.
     *
     * @return void
     */
    public function progressAdvance(int $step = 1);
    /**
     * Finishes the progress output.
     *
     * @return void
     */
    public function progressFinish();
}
