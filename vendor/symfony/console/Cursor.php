<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Pierre du Plessis <pdples@gmail.com>
 */
final class Cursor
{
    private $output;
    private $input;

    public function __construct(OutputInterface $output, $input = null)
    {
        $this->output = $output;
        $this->input = isset($input) ? $input : (\defined('STDIN') ? \STDIN : fopen('php://input', 'r+'));
    }

    /**
     * @return $this
     * @param int $lines
     */
    public function moveUp($lines = 1)
    {
        $lines = (int) $lines;
        $this->output->write(sprintf("\x1b[%dA", $lines));

        return $this;
    }

    /**
     * @return $this
     * @param int $lines
     */
    public function moveDown($lines = 1)
    {
        $lines = (int) $lines;
        $this->output->write(sprintf("\x1b[%dB", $lines));

        return $this;
    }

    /**
     * @return $this
     * @param int $columns
     */
    public function moveRight($columns = 1)
    {
        $columns = (int) $columns;
        $this->output->write(sprintf("\x1b[%dC", $columns));

        return $this;
    }

    /**
     * @return $this
     * @param int $columns
     */
    public function moveLeft($columns = 1)
    {
        $columns = (int) $columns;
        $this->output->write(sprintf("\x1b[%dD", $columns));

        return $this;
    }

    /**
     * @return $this
     * @param int $column
     */
    public function moveToColumn($column)
    {
        $column = (int) $column;
        $this->output->write(sprintf("\x1b[%dG", $column));

        return $this;
    }

    /**
     * @return $this
     * @param int $column
     * @param int $row
     */
    public function moveToPosition($column, $row)
    {
        $column = (int) $column;
        $row = (int) $row;
        $this->output->write(sprintf("\x1b[%d;%dH", $row + 1, $column));

        return $this;
    }

    /**
     * @return $this
     */
    public function savePosition()
    {
        $this->output->write("\x1b7");

        return $this;
    }

    /**
     * @return $this
     */
    public function restorePosition()
    {
        $this->output->write("\x1b8");

        return $this;
    }

    /**
     * @return $this
     */
    public function hide()
    {
        $this->output->write("\x1b[?25l");

        return $this;
    }

    /**
     * @return $this
     */
    public function show()
    {
        $this->output->write("\x1b[?25h\x1b[?0c");

        return $this;
    }

    /**
     * Clears all the output from the current line.
     * @return $this
     */
    public function clearLine()
    {
        $this->output->write("\x1b[2K");

        return $this;
    }

    /**
     * Clears all the output from the current line after the current position.
     * @return $this
     */
    public function clearLineAfter()
    {
        $this->output->write("\x1b[K");

        return $this;
    }

    /**
     * Clears all the output from the cursors' current position to the end of the screen.
     * @return $this
     */
    public function clearOutput()
    {
        $this->output->write("\x1b[0J");

        return $this;
    }

    /**
     * Clears the entire screen.
     * @return $this
     */
    public function clearScreen()
    {
        $this->output->write("\x1b[2J");

        return $this;
    }

    /**
     * Returns the current cursor position as x,y coordinates.
     * @return mixed[]
     */
    public function getCurrentPosition()
    {
        static $isTtySupported;

        if (null === $isTtySupported && \function_exists('proc_open')) {
            $isTtySupported = (bool) @proc_open('echo 1 >/dev/null', [['file', '/dev/tty', 'r'], ['file', '/dev/tty', 'w'], ['file', '/dev/tty', 'w']], $pipes);
        }

        if (!$isTtySupported) {
            return [1, 1];
        }

        $sttyMode = shell_exec('stty -g');
        shell_exec('stty -icanon -echo');

        @fwrite($this->input, "\033[6n");

        $code = trim(fread($this->input, 1024));

        shell_exec(sprintf('stty %s', $sttyMode));

        sscanf($code, "\033[%d;%dR", $row, $col);

        return [$col, $row];
    }
}
