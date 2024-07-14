<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symplify\EasyCodingStandard\Caching\FileHashComputer;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractTestCase;

/**
 * Testing file hashing is a bit of a chore as it uses a lot of global state
 * that is non-trivial to mock or reset. And it's not enough to isolate each
 * test case, we need to isolate each invocation of hash. Since we need to
 * actually compare hashes to properly test... that's a problem.
 *
 * So the way this class works, each test is run in a separate PHP process that
 * does NOT inherit static property state for classes. For each test case that
 * needs to check an ECS config hash, we create a `Fixture/ecs.*.php` file with
 * associated `testHash*` "test" methods that return the hash of that file, and
 * then we create the *actual* test method for that case that does the
 * assertions, depending on the hashing "test" methods. This isn't perfect,
 * since we don't really want all those hash methods to actually be tests, but
 * it works.
 *
 * This approach was settled on after attempting:
 *
 * 1. To #[Depends] on a test that used a #[DataProvider]. But tests with data
 *    providers have their return forcibly overwritten to `null`, instead of
 *    providing the dependant tests the array of the results they generate for
 *    all data provider sets.
 * 2. To use a forking library to manually do the same. But this would either
 *    require manually re-implementing #[RunInSeparateProcess] *OR* requiring
 *    all contributors to have `ext-pcntl`/`ext-sockets` enabled.
 * 3. Manually overriding global state for the parameters provider and service
 *    containers. However, this quickly ballooned in complexity and was
 *    considered non-viable for maintenance.
 *
 * @todo This really should test differing package versions also invalidate the
 *      cache, but it would require refactoring those methods being static,
 *      since we can't mock them otherwise.
 */
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class FileHashComputerTest extends AbstractTestCase
{
    public function testHashDifferentFixerConfig1(): string
    {
        return $this->hash('different-fixer-config.1');
    }

    public function testHashDifferentFixerConfig2(): string
    {
        return $this->hash('different-fixer-config.2');
    }

    public function testHashDifferentSniffConfig1(): string
    {
        return $this->hash('different-sniff-config.1');
    }

    public function testHashDifferentSniffConfig2(): string
    {
        return $this->hash('different-sniff-config.2');
    }

    public function testHashDifferentFixerRules1(): string
    {
        return $this->hash('different-fixer-rules.1');
    }

    public function testHashDifferentFixerRules2(): string
    {
        return $this->hash('different-fixer-rules.2');
    }

    public function testHashDifferentSniffRules1(): string
    {
        return $this->hash('different-sniff-rules.1');
    }

    public function testHashDifferentSniffRules2(): string
    {
        return $this->hash('different-sniff-rules.2');
    }

    public function testHashDifferentThirdPartyFixerConfig1(): string
    {
        return $this->hash('different-third-party-fixer-config.1');
    }

    public function testHashDifferentThirdPartyFixerConfig2(): string
    {
        return $this->hash('different-third-party-fixer-config.2');
    }

    public function testHashExactSame1(): string
    {
        return $this->hash('exact-same.1');
    }

    public function testHashExactSame2(): string
    {
        return $this->hash('exact-same.2');
    }

    public function testHashSimilar1(): string
    {
        return $this->hash('similar.1');
    }

    public function testHashSimilar2(): string
    {
        return $this->hash('similar.2');
    }

    public function testHashDifferentServices1(): string
    {
        return $this->hash('different-services.1');
    }

    public function testHashDifferentServices2(): string
    {
        return $this->hash('different-services.2');
    }

    #[Depends('testHashExactSame1')]
    #[Depends('testHashExactSame2')]
    public function testExactSameConfigsMatch(string $hash1, string $hash2): void
    {
        $this->assertEquals($hash1, $hash2);
    }

    #[Depends('testHashSimilar1')]
    #[Depends('testHashSimilar2')]
    public function testSimilarConfigsMatch(string $hash1, string $hash2): void
    {
        $this->assertEquals($hash1, $hash2);
    }

    /**
     * Tests the special code path for Configurable fixers that don't extend the `AbstractFixer`.
     *
     * @see FileHashComputer::getFixerConfiguration
     */
    #[Depends('testHashDifferentThirdPartyFixerConfig1')]
    #[Depends('testHashDifferentThirdPartyFixerConfig2')]
    public function testThirdPartyFixerConfigsCauseInvalidation(string $hash1, string $hash2): void
    {
        $this->assertNotEquals($hash1, $hash2);
    }

    #[Depends('testHashDifferentFixerConfig1')]
    #[Depends('testHashDifferentFixerConfig2')]
    public function testFirstPartyFixerConfigsCauseInvalidation(string $hash1, string $hash2): void
    {
        $this->assertNotEquals($hash1, $hash2);
    }

    #[Depends('testHashDifferentSniffConfig1')]
    #[Depends('testHashDifferentSniffConfig2')]
    public function testSnifferConfigsCauseInvalidation(string $hash1, string $hash2): void
    {
        $this->assertNotEquals($hash1, $hash2);
    }

    #[Depends('testHashDifferentSniffRules1')]
    #[Depends('testHashDifferentSniffRules2')]
    public function testDifferentSniffRulesCauseInvalidation(string $hash1, string $hash2): void
    {
        $this->assertNotEquals($hash1, $hash2);
    }

    #[Depends('testHashDifferentFixerRules1')]
    #[Depends('testHashDifferentFixerRules2')]
    public function testDifferentFixerRulesCauseInvalidation(string $hash1, string $hash2): void
    {
        $this->assertNotEquals($hash1, $hash2);
    }

    #[Depends('testHashDifferentServices1')]
    #[Depends('testHashDifferentServices2')]
    public function testDifferentServiceConfigsCauseInvalidation(string $hash1, string $hash2): void
    {
        $this->assertNotEquals($hash1, $hash2);
    }

    public function testPhpFileHash(): void
    {
        $fileHashComputer = new FileHashComputer();

        $fileOne = __DIR__ . '/Fixture/Source/SomeScannedClass.php';
        $fileOneHash = $fileHashComputer->compute($fileOne);

        $expectedFileOneHasn = md5_file($fileOne);
        $this->assertSame($expectedFileOneHasn, $fileOneHash);

        $fileTwo = __DIR__ . '/Fixture/Source/ChangedScannedClass.php';
        $fileTwoHash = $fileHashComputer->compute($fileTwo);

        $expectedFileTwoHash = md5_file($fileTwo);
        $this->assertSame($expectedFileTwoHash, $fileTwoHash);

        $this->assertNotSame($fileOneHash, $fileTwoHash);
    }

    private function hash(string $name): string
    {
        $fileHashComputer = new FileHashComputer();
        $hash = $fileHashComputer->computeConfig($this->configPath($name));

        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);

        return $hash;
    }

    private function configPath(string $name): string
    {
        return sprintf('%s/Fixture/Config/ecs.%s.php', __DIR__, $name);
    }
}
