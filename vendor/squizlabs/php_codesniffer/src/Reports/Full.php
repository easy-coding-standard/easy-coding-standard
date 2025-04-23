<?php

/**
 * Full report for PHP_CodeSniffer.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Timing;
class Full implements \PHP_CodeSniffer\Reports\Report
{
    /**
     * Generate a partial report for a single processed file.
     *
     * Function should return TRUE if it printed or stored data about the file
     * and FALSE if it ignored the file. Returning TRUE indicates that the file and
     * its data should be counted in the grand totals.
     *
     * @param array<string, string|int|array> $report      Prepared report data.
     *                                                     See the {@see Report} interface for a detailed specification.
     * @param \PHP_CodeSniffer\Files\File     $phpcsFile   The file being reported on.
     * @param bool                            $showSources Show sources?
     * @param int                             $width       Maximum allowed line width.
     *
     * @return bool
     */
    public function generateFileReport($report, File $phpcsFile, $showSources = \false, $width = 80)
    {
        if ($report['errors'] === 0 && $report['warnings'] === 0) {
            // Nothing to print.
            return \false;
        }
        // The length of the word ERROR or WARNING; used for padding.
        if ($report['warnings'] > 0) {
            $typeLength = 7;
        } else {
            $typeLength = 5;
        }
        // Work out the max line number length for formatting.
        $maxLineNumLength = \max(\array_map('strlen', \array_keys($report['messages'])));
        // The padding that all lines will require that are
        // printing an error message overflow.
        $paddingLine2 = \str_repeat(' ', $maxLineNumLength + 1);
        $paddingLine2 .= ' | ';
        $paddingLine2 .= \str_repeat(' ', $typeLength);
        $paddingLine2 .= ' | ';
        if ($report['fixable'] > 0) {
            $paddingLine2 .= '    ';
        }
        $paddingLength = \strlen($paddingLine2);
        // Make sure the report width isn't too big.
        $maxErrorLength = 0;
        foreach ($report['messages'] as $lineErrors) {
            foreach ($lineErrors as $colErrors) {
                foreach ($colErrors as $error) {
                    // Start with the presumption of a single line error message.
                    $length = \strlen($error['message']);
                    $srcLength = \strlen($error['source']) + 3;
                    if ($showSources === \true) {
                        $length += $srcLength;
                    }
                    // ... but also handle multi-line messages correctly.
                    if (\strpos($error['message'], "\n") !== \false) {
                        $errorLines = \explode("\n", $error['message']);
                        $length = \max(\array_map('strlen', $errorLines));
                        if ($showSources === \true) {
                            $lastLine = \array_pop($errorLines);
                            $length = \max($length, \strlen($lastLine) + $srcLength);
                        }
                    }
                    $maxErrorLength = \max($maxErrorLength, $length + 1);
                }
                //end foreach
            }
            //end foreach
        }
        //end foreach
        $file = $report['filename'];
        $fileLength = \strlen($file);
        $maxWidth = \max($fileLength + 6, $maxErrorLength + $paddingLength);
        $width = \min($width, $maxWidth);
        if ($width < 70) {
            $width = 70;
        }
        echo \PHP_EOL . "\x1b[1mFILE: ";
        if ($fileLength <= $width - 6) {
            echo $file;
        } else {
            echo '...' . \substr($file, $fileLength - ($width - 6));
        }
        echo "\x1b[0m" . \PHP_EOL;
        echo \str_repeat('-', $width) . \PHP_EOL;
        echo "\x1b[1m" . 'FOUND ' . $report['errors'] . ' ERROR';
        if ($report['errors'] !== 1) {
            echo 'S';
        }
        if ($report['warnings'] > 0) {
            echo ' AND ' . $report['warnings'] . ' WARNING';
            if ($report['warnings'] !== 1) {
                echo 'S';
            }
        }
        echo ' AFFECTING ' . \count($report['messages']) . ' LINE';
        if (\count($report['messages']) !== 1) {
            echo 'S';
        }
        echo "\x1b[0m" . \PHP_EOL;
        echo \str_repeat('-', $width) . \PHP_EOL;
        // The maximum amount of space an error message can use.
        $maxErrorSpace = $width - $paddingLength - 1;
        $beforeMsg = '';
        $afterMsg = '';
        if ($showSources === \true) {
            $beforeMsg = "\x1b[1m";
            $afterMsg = "\x1b[0m";
        }
        $beforeAfterLength = \strlen($beforeMsg . $afterMsg);
        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $colErrors) {
                foreach ($colErrors as $error) {
                    $errorMsg = \wordwrap($error['message'], $maxErrorSpace);
                    // Add the padding _after_ the wordwrap as the message itself may contain line breaks
                    // and those lines will also need to receive padding.
                    $errorMsg = \str_replace("\n", $afterMsg . \PHP_EOL . $paddingLine2 . $beforeMsg, $errorMsg);
                    $errorMsg = $beforeMsg . $errorMsg . $afterMsg;
                    if ($showSources === \true) {
                        $lastMsg = $errorMsg;
                        $startPosLastLine = \strrpos($errorMsg, \PHP_EOL . $paddingLine2 . $beforeMsg);
                        if ($startPosLastLine !== \false) {
                            // Message is multiline. Grab the text of last line of the message, including the color codes.
                            $lastMsg = \substr($errorMsg, $startPosLastLine + \strlen(\PHP_EOL . $paddingLine2));
                        }
                        // When show sources is used, the message itself will be bolded, so we need to correct the length.
                        $sourceSuffix = '(' . $error['source'] . ')';
                        $lastMsgPlusSourceLength = \strlen($lastMsg);
                        // Add space + source suffix length.
                        $lastMsgPlusSourceLength += 1 + \strlen($sourceSuffix);
                        // Correct for the color codes.
                        $lastMsgPlusSourceLength -= $beforeAfterLength;
                        if ($lastMsgPlusSourceLength > $maxErrorSpace) {
                            $errorMsg .= \PHP_EOL . $paddingLine2 . $sourceSuffix;
                        } else {
                            $errorMsg .= ' ' . $sourceSuffix;
                        }
                    }
                    //end if
                    // The padding that goes on the front of the line.
                    $padding = $maxLineNumLength - \strlen($line);
                    echo ' ' . \str_repeat(' ', $padding) . $line . ' | ';
                    if ($error['type'] === 'ERROR') {
                        echo "\x1b[31mERROR\x1b[0m";
                        if ($report['warnings'] > 0) {
                            echo '  ';
                        }
                    } else {
                        echo "\x1b[33mWARNING\x1b[0m";
                    }
                    echo ' | ';
                    if ($report['fixable'] > 0) {
                        echo '[';
                        if ($error['fixable'] === \true) {
                            echo 'x';
                        } else {
                            echo ' ';
                        }
                        echo '] ';
                    }
                    echo $errorMsg . \PHP_EOL;
                }
                //end foreach
            }
            //end foreach
        }
        //end foreach
        echo \str_repeat('-', $width) . \PHP_EOL;
        if ($report['fixable'] > 0) {
            echo "\x1b[1m" . 'PHPCBF CAN FIX THE ' . $report['fixable'] . ' MARKED SNIFF VIOLATIONS AUTOMATICALLY' . "\x1b[0m" . \PHP_EOL;
            echo \str_repeat('-', $width) . \PHP_EOL;
        }
        echo \PHP_EOL;
        return \true;
    }
    //end generateFileReport()
    /**
     * Prints all errors and warnings for each file processed.
     *
     * @param string $cachedData    Any partial report data that was returned from
     *                              generateFileReport during the run.
     * @param int    $totalFiles    Total number of files processed during the run.
     * @param int    $totalErrors   Total number of errors found during the run.
     * @param int    $totalWarnings Total number of warnings found during the run.
     * @param int    $totalFixable  Total number of problems that can be fixed.
     * @param bool   $showSources   Show sources?
     * @param int    $width         Maximum allowed line width.
     * @param bool   $interactive   Are we running in interactive mode?
     * @param bool   $toScreen      Is the report being printed to screen?
     *
     * @return void
     */
    public function generate($cachedData, $totalFiles, $totalErrors, $totalWarnings, $totalFixable, $showSources = \false, $width = 80, $interactive = \false, $toScreen = \true)
    {
        if ($cachedData === '') {
            return;
        }
        echo $cachedData;
        if ($toScreen === \true && $interactive === \false) {
            Timing::printRunTime();
        }
    }
    //end generate()
}
//end class
