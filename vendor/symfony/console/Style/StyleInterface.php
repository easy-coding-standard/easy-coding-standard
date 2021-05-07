<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\Console\Style;

/**
 * Output style helpers.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface StyleInterface
{
    /**
     * Formats a command title.
     * @param string $message
     */
    public function title($message);
    /**
     * Formats a section title.
     * @param string $message
     */
    public function section($message);
    /**
     * Formats a list.
     */
    public function listing(array $elements);
    /**
     * Formats informational text.
     *
     * @param string|array $message
     */
    public function text($message);
    /**
     * Formats a success result bar.
     *
     * @param string|array $message
     */
    public function success($message);
    /**
     * Formats an error result bar.
     *
     * @param string|array $message
     */
    public function error($message);
    /**
     * Formats an warning result bar.
     *
     * @param string|array $message
     */
    public function warning($message);
    /**
     * Formats a note admonition.
     *
     * @param string|array $message
     */
    public function note($message);
    /**
     * Formats a caution admonition.
     *
     * @param string|array $message
     */
    public function caution($message);
    /**
     * Formats a table.
     */
    public function table(array $headers, array $rows);
    /**
     * Asks a question.
     *
     * @return mixed
     * @param callable|null $validator
     * @param string|null $default
     * @param string $question
     */
    public function ask($question, $default = null, $validator = null);
    /**
     * Asks a question with the user input hidden.
     *
     * @return mixed
     * @param callable|null $validator
     * @param string $question
     */
    public function askHidden($question, $validator = null);
    /**
     * Asks for confirmation.
     *
     * @return bool
     * @param string $question
     * @param bool $default
     */
    public function confirm($question, $default = \true);
    /**
     * Asks a choice question.
     *
     * @param string|int|null $default
     *
     * @return mixed
     * @param string $question
     */
    public function choice($question, array $choices, $default = null);
    /**
     * Add newline(s).
     * @param int $count
     */
    public function newLine($count = 1);
    /**
     * Starts the progress output.
     * @param int $max
     */
    public function progressStart($max = 0);
    /**
     * Advances the progress output X steps.
     * @param int $step
     */
    public function progressAdvance($step = 1);
    /**
     * Finishes the progress output.
     */
    public function progressFinish();
}
