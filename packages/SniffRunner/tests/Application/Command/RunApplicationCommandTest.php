<?php declare(strict_types=1);

namespace Symplify\SniffRunner\Tests\Application\Command;

use PHPUnit\Framework\TestCase;
use Symplify\SniffRunner\Application\Command\RunApplicationCommand;

final class RunApplicationCommandTest extends TestCase
{
    public function test()
    {
        $command = new RunApplicationCommand(
            $source = [__DIR__],
            $standards = ['standards'],
            $sniffs = ['sniffs'],
            $excludedSniffs = ['excluded-sniffs'],
            $isFixer = true
        );

        $this->assertSame($excludedSniffs, $command->getExcludedSniffs());
        $this->assertSame($source, $command->getSource());
        $this->assertSame($standards, $command->getStandards());
        $this->assertSame($sniffs, $command->getSniffs());
        $this->assertSame($excludedSniffs, $command->getExcludedSniffs());
        $this->assertSame($isFixer, $command->isFixer());
    }
}
