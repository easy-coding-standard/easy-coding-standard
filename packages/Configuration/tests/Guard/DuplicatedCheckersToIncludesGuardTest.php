<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\Exception\Guard\DuplicatedCheckersLoadedException;
use Symplify\EasyCodingStandard\Configuration\Guard\DuplicatedCheckersToIncludesGuard;

final class DuplicatedCheckersToIncludesGuardTest extends TestCase
{
    /**
     * @var DuplicatedCheckersToIncludesGuard
     */
    private $duplicatedCheckersToIncludesGuard;

    protected function setUp(): void
    {
        $this->duplicatedCheckersToIncludesGuard = new DuplicatedCheckersToIncludesGuard();
    }

    /**
     * @dataProvider provideConflictingConfigFiles()
     */
    public function testConflicting(string $configFile): void
    {
        $this->expectException(DuplicatedCheckersLoadedException::class);
        $this->duplicatedCheckersToIncludesGuard->processConfigFile($configFile);
    }

    /**
     * @return string[]
     */
    public function provideConflictingConfigFiles(): array
    {
       return [
           [__DIR__ . '/DuplicatedCheckersToIncludesGuardSource/config-with-conflict.1.neon']
       ];
    }


    /**
     * @dataProvider provideValidConfigFiles()
     */
    public function testValid(string $configFile): void
    {
        $this->duplicatedCheckersToIncludesGuard->processConfigFile($configFile);

        // just check it passes without exception
        $this->assertTrue(true);
    }

    /**
     * @return string[]
     */
    public function provideValidConfigFiles(): array
    {
        return [
            [__DIR__ . '/DuplicatedCheckersToIncludesGuardSource/valid-config.1.neon']
        ];
    }
}
