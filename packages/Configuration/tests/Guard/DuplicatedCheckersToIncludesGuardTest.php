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
    public function test(string $configFile): void
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
           [__DIR__ . '/DuplicatedCheckersToIncludesGuardSource/config1.neon']
       ];
    }
}
