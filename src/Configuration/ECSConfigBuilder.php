<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\Exception\Configuration\SuperfluousConfigurationException;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

/**
 * @api
 */
final class ECSConfigBuilder
{
    /**
     * @var string[]
     */
    private array $paths = [];

    /**
     * @var string[]
     */
    private array $sets = [];

    /**
     * @var string[]
     */
    private array $dynamicSets = [];

    /**
     * @var array<mixed>
     */
    private array $skip = [];

    /**
     * @var array<class-string<Sniff|FixerInterface>>
     */
    private array $rules = [];

    /**
     * @var array<class-string<(FixerInterface | Sniff)>, mixed>
     */
    private array $rulesWithConfiguration = [];

    public function __invoke(ECSConfig $ecsConfig): void
    {
        $ecsConfig->sets($this->sets);
        $ecsConfig->paths($this->paths);
        $ecsConfig->skip($this->skip);
        $ecsConfig->rules($this->rules);
        $ecsConfig->rulesWithConfiguration($this->rulesWithConfiguration);

        $ecsConfig->dynamicSets($this->dynamicSets);
    }

    /**
     * @param string[] $paths
     */
    public function withPaths(array $paths): self
    {
        $this->paths = $paths;

        return $this;
    }

    /**
     * @param array<mixed> $skip
     */
    public function withSkip(array $skip): self
    {
        $this->skip = $skip;

        return $this;
    }

    /**
     * Include PHP files from the root directory,
     * typically ecs.php, rector.php etc.
     */
    public function withRootFiles(): self
    {
        $rootPhpFilesFinder = (new Finder())->files()
            ->in(getcwd())
            ->depth(0)
            ->name('*.php');

        foreach ($rootPhpFilesFinder as $rootPhpFileFinder) {
            $this->paths[] = $rootPhpFileFinder->getRealPath();
        }

        return $this;
    }

    public function withPreparedSets(
        /** @see SetList::PSR_12 */
        bool $psr12 = false,
        /** @see SetList::COMMON */
        bool $common = false,
        /** @see SetList::SYMPLIFY */
        bool $symplify = false,
        // common sets
        /** @see SetList::ARRAY */
        bool $arrays = false,
        /** @see SetList::COMMENTS */
        bool $comments = false,
        /** @see SetList::DOCBLOCK */
        bool $docblocks = false,
        /** @see SetList::SPACES */
        bool $spaces = false,
        /** @see SetList::NAMESPACES */
        bool $namespaces = false,
        /** @see SetList::CONTROL_STRUCTURES */
        bool $controlStructures = false,
        /** @see SetList::PHPUNIT */
        bool $phpunit = false,
        /** @see SetList::STRICT */
        bool $strict = false,
    ): self {
        if ($psr12) {
            $this->sets[] = SetList::PSR_12;
        }

        if ($common) {
            // include all "common" sets
            $this->sets[] = SetList::COMMON;

            if ($arrays || $spaces || $namespaces || $docblocks || $controlStructures || $phpunit || $strict || $comments) {
                throw new SuperfluousConfigurationException(
                    'This set is already included in the "common" set. You can remove it'
                );
            }
        } else {
            if ($arrays) {
                $this->sets[] = SetList::ARRAY;
            }

            if ($spaces) {
                $this->sets[] = SetList::SPACES;
            }

            if ($namespaces) {
                $this->sets[] = SetList::NAMESPACES;
            }

            if ($docblocks) {
                $this->sets[] = SetList::DOCBLOCK;
            }

            if ($controlStructures) {
                $this->sets[] = SetList::CONTROL_STRUCTURES;
            }

            if ($phpunit) {
                $this->sets[] = SetList::PHPUNIT;
            }

            if ($strict) {
                $this->sets[] = SetList::STRICT;
            }

            if ($comments) {
                $this->sets[] = SetList::COMMENTS;
            }
        }

        if ($symplify) {
            $this->sets[] = SetList::SYMPLIFY;
        }

        return $this;
    }

    public function withPreparedPhpCsFixerSets(
        bool $doctrineAnnotation = false,
        bool $per = false,
        bool $perCS = false,
        bool $perCS10 = false,
        bool $perCS10Risky = false,
        bool $perCS20 = false,
        bool $perCS20Risky = false,
        bool $perCSRisky = false,
        bool $perRisky = false,
        bool $php54Migration = false,
        bool $php56MigrationRisky = false,
        bool $php70Migration = false,
        bool $php70MigrationRisky = false,
        bool $php71Migration = false,
        bool $php71MigrationRisky = false,
        bool $php73Migration = false,
        bool $php74Migration = false,
        bool $php74MigrationRisky = false,
        bool $php80Migration = false,
        bool $php80MigrationRisky = false,
        bool $php81Migration = false,
        bool $php82Migration = false,
        bool $php83Migration = false,
        bool $phpunit30MigrationRisky = false,
        bool $phpunit32MigrationRisky = false,
        bool $phpunit35MigrationRisky = false,
        bool $phpunit43MigrationRisky = false,
        bool $phpunit48MigrationRisky = false,
        bool $phpunit50MigrationRisky = false,
        bool $phpunit52MigrationRisky = false,
        bool $phpunit54MigrationRisky = false,
        bool $phpunit55MigrationRisky = false,
        bool $phpunit56MigrationRisky = false,
        bool $phpunit57MigrationRisky = false,
        bool $phpunit60MigrationRisky = false,
        bool $phpunit75MigrationRisky = false,
        bool $phpunit84MigrationRisky = false,
        bool $phpunit100MigrationRisky = false,
        bool $psr1 = false,
        bool $psr2 = false,
        bool $psr12 = false,
        bool $psr12Risky = false,
        bool $phpCsFixer = false,
        bool $phpCsFixerRisky = false,
        bool $symfony = false,
        bool $symfonyRisky = false
    ): self {
        if ($doctrineAnnotation) {
            $this->dynamicSets[] = '@DoctrineAnnotation';
        }

        if ($per) {
            $this->dynamicSets[] = '@PER';
        }

        if ($perCS) {
            $this->dynamicSets[] = '@PER-CS';
        }

        if ($perCS10) {
            $this->dynamicSets[] = '@PER-CS1.0';
        }

        if ($perCS10Risky) {
            $this->dynamicSets[] = '@PER-CS1.0:risky';
        }

        if ($perCS20) {
            $this->dynamicSets[] = '@PER-CS2.0';
        }

        if ($perCS20Risky) {
            $this->dynamicSets[] = '@PER-CS2.0:risky';
        }

        if ($perCSRisky) {
            $this->dynamicSets[] = '@PER-CS:risky';
        }

        if ($perRisky) {
            $this->dynamicSets[] = '@PER:risky';
        }

        if ($php54Migration) {
            $this->dynamicSets[] = '@PHP54Migration';
        }

        if ($php56MigrationRisky) {
            $this->dynamicSets[] = '@PHP56Migration:risky';
        }

        if ($php70Migration) {
            $this->dynamicSets[] = '@PHP70Migration';
        }

        if ($php70MigrationRisky) {
            $this->dynamicSets[] = '@PHP70Migration:risky';
        }

        if ($php71Migration) {
            $this->dynamicSets[] = '@PHP71Migration';
        }

        if ($php71MigrationRisky) {
            $this->dynamicSets[] = '@PHP71Migration:risky';
        }

        if ($php73Migration) {
            $this->dynamicSets[] = '@PHP73Migration';
        }

        if ($php74Migration) {
            $this->dynamicSets[] = '@PHP74Migration';
        }

        if ($php74MigrationRisky) {
            $this->dynamicSets[] = '@PHP74Migration:risky';
        }

        if ($php80Migration) {
            $this->dynamicSets[] = '@PHP80Migration';
        }

        if ($php80MigrationRisky) {
            $this->dynamicSets[] = '@PHP80Migration:risky';
        }

        if ($php81Migration) {
            $this->dynamicSets[] = '@PHP81Migration';
        }

        if ($php82Migration) {
            $this->dynamicSets[] = '@PHP82Migration';
        }

        if ($php83Migration) {
            $this->dynamicSets[] = '@PHP83Migration';
        }

        if ($phpunit30MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit30Migration:risky';
        }

        if ($phpunit32MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit32Migration:risky';
        }

        if ($phpunit35MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit35Migration:risky';
        }

        if ($phpunit43MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit43Migration:risky';
        }

        if ($phpunit48MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit48Migration:risky';
        }

        if ($phpunit50MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit50Migration:risky';
        }

        if ($phpunit52MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit52Migration:risky';
        }

        if ($phpunit54MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit54Migration:risky';
        }

        if ($phpunit55MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit55Migration:risky';
        }

        if ($phpunit56MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit56Migration:risky';
        }

        if ($phpunit57MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit57Migration:risky';
        }

        if ($phpunit60MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit60Migration:risky';
        }

        if ($phpunit75MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit75Migration:risky';
        }

        if ($phpunit84MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit84Migration:risky';
        }

        if ($phpunit100MigrationRisky) {
            $this->dynamicSets[] = '@PHPUnit100Migration:risky';
        }

        if ($psr1) {
            $this->dynamicSets[] = '@PSR1';
        }

        if ($psr2) {
            $this->dynamicSets[] = '@PSR2';
        }

        if ($psr12) {
            $this->dynamicSets[] = '@PSR12';
        }

        if ($psr12Risky) {
            $this->dynamicSets[] = '@PSR12:risky';
        }

        if ($phpCsFixer) {
            $this->dynamicSets[] = '@PhpCsFixer';
        }

        if ($phpCsFixerRisky) {
            $this->dynamicSets[] = '@PhpCsFixer:risky';
        }

        if ($symfony) {
            $this->dynamicSets[] = '@Symfony';
        }

        if ($symfonyRisky) {
            $this->dynamicSets[] = '@Symfony:risky';
        }

        return $this;
    }

    /**
     * @param string[] $sets
     */
    public function withSets(array $sets): self
    {
        $this->sets = [...$this->sets, ...$sets];

        return $this;
    }

    /**
     * @param array<class-string<Sniff|FixerInterface>> $rules
     */
    public function withRules(array $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @param array<class-string<(FixerInterface | Sniff)>, mixed> $configuredRules
     */
    public function withConfiguredRules(array $configuredRules): self
    {
        $this->rulesWithConfiguration = $configuredRules;

        return $this;
    }
}
