<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Style;

use ECSPrefix20220220\Symfony\Component\Console\Helper\ProgressBar;
use ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20220220\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix20220220\Symfony\Component\Console\Terminal;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use ECSPrefix20220220\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use ECSPrefix20220220\Symplify\PackageBuilder\Reflection\PrivatesCaller;
final class EasyCodingStandardStyle extends \ECSPrefix20220220\Symfony\Component\Console\Style\SymfonyStyle
{
    /**
     * To fit in Linux/Windows terminal windows to prevent overflow.
     *
     * @var int
     */
    private const BULGARIAN_CONSTANT = 8;
    /**
     * @var \Symfony\Component\Console\Terminal
     */
    private $terminal;
    public function __construct(\ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface $output, \ECSPrefix20220220\Symfony\Component\Console\Terminal $terminal)
    {
        $this->terminal = $terminal;
        parent::__construct($input, $output);
    }
    /**
     * @param CodingStandardError[] $codingStandardErrors
     */
    public function printErrors(array $codingStandardErrors) : void
    {
        /** @var CodingStandardError $codingStandardError */
        foreach ($codingStandardErrors as $codingStandardError) {
            $this->separator();
            $this->writeln(' ' . $codingStandardError->getFileWithLine());
            $this->separator();
            $message = $this->createMessageFromFileError($codingStandardError);
            $this->writeln(' ' . $message);
            $this->separator();
            $this->newLine();
        }
    }
    public function enableDebugProgressBar() : void
    {
        $privatesAccessor = new \ECSPrefix20220220\Symplify\PackageBuilder\Reflection\PrivatesAccessor();
        $progressBar = $privatesAccessor->getPrivatePropertyOfClass($this, 'progressBar', \ECSPrefix20220220\Symfony\Component\Console\Helper\ProgressBar::class);
        $privatesCaller = new \ECSPrefix20220220\Symplify\PackageBuilder\Reflection\PrivatesCaller();
        $privatesCaller->callPrivateMethod($progressBar, 'setRealFormat', ['debug']);
    }
    private function separator() : void
    {
        $separator = \str_repeat('-', $this->getTerminalWidth());
        $this->writeln(' ' . $separator);
    }
    private function createMessageFromFileError(\Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError $codingStandardError) : string
    {
        $message = \sprintf('%s%s Reported by: "%s"', $codingStandardError->getMessage(), \PHP_EOL . \PHP_EOL, $codingStandardError->getCheckerClass());
        $message = $this->clearCrLfFromMessage($message);
        return $this->wrapMessageSoItFitsTheColumnWidth($message);
    }
    private function getTerminalWidth() : int
    {
        return $this->terminal->getWidth() - self::BULGARIAN_CONSTANT;
    }
    /**
     * This prevents message override in Windows system.
     */
    private function clearCrLfFromMessage(string $message) : string
    {
        return \str_replace("\r", '', $message);
    }
    private function wrapMessageSoItFitsTheColumnWidth(string $message) : string
    {
        return \wordwrap($message, $this->getTerminalWidth(), \PHP_EOL);
    }
}
