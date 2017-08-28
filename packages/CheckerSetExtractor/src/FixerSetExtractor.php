<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\CheckerSetExtractor;

use Nette\Utils\Strings;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\RuleSet;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\CheckerSetExtractor\Exception\MissingFixerSetException;
use Throwable;

final class FixerSetExtractor
{
    /**
     * @var string[]
     */
    private $fixerNameToClassMap = [];

    /**
     * @return string[]
     */
    public function extract(string $name): array
    {
        $name = $this->normalizeName($name);

        try {
            $fixerSet = RuleSet::create([$name => true]);
        } catch (Throwable $throwable) {
            $availableFixerSetNames = RuleSet::create()->getSetDefinitionNames();
            throw new MissingFixerSetException(sprintf(
                'Set "%s" was not found. Try one of: "%s.',
                $name,
                implode(', ', $availableFixerSetNames)
            ));
        }

        $fixerNames = $fixerSet->getRules();

        return $this->convertFixerNamesToFixerClasses($fixerNames);
    }

    /**
     * @param string[]
     * @return mixed[]
     */
    private function convertFixerNamesToFixerClasses(array $fixerNames): array
    {
        $fixers = [];

        foreach ($fixerNames as $fixerName => $config) {
            $fixerClass = $this->getFixerClassByName($fixerName);
            $fixers[$fixerClass] = $config;
        }

        return $fixers;
    }

    private function normalizeName(string $name): string
    {
        if (stripos($name, 'psr') !== false) {
            $name = strtoupper($name);
        }

        if (stripos($name, 'symfony') !== false) {
            $name = ucfirst(ltrim(strtolower($name), '@'));
        }

        if (Strings::startsWith($name, '@')) {
            return $name;
        }

        return '@' . $name;
    }

    private function getFixerClassByName(string $fixerName): string
    {
        return $this->getFixerNameToClassMap()[$fixerName];
    }

    /**
     * @return string[]
     */
    private function getFixerNameToClassMap(): array
    {
        if ($this->fixerNameToClassMap) {
            return $this->fixerNameToClassMap;
        }

        $finder = $this->findAllBuiltInFixers();
        $fixerNameToClassMap = [];

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $fixerClass = $this->getFixerClassFromFileName($file);
            $fixerName = $this->getFixerNameFromClass($fixerClass);
            $fixerNameToClassMap[$fixerName] = $fixerClass;
        }

        return $this->fixerNameToClassMap = $fixerNameToClassMap;
    }

    /**
     * @return SplFileInfo[]
     */
    private function findAllBuiltInFixers(): array
    {
        $fixerInterfaceReflection = new ReflectionClass(FixerInterface::class);
        $fixersDirectory = dirname($fixerInterfaceReflection->getFileName());

        $finderIterator = Finder::create()->files()
            ->name('*Fixer.php')
            ->in($fixersDirectory)
            ->getIterator();

        return iterator_to_array($finderIterator);
    }

    private function getFixerClassFromFileName(SplFileInfo $file): string
    {
        $relativeNamespace = $file->getRelativePath();

        return 'PhpCsFixer\\Fixer\\' . ($relativeNamespace . '\\') . $file->getBasename('.php');
    }

    private function getFixerNameFromClass(string $fixerClass): string
    {
        $fixerReflection = new ReflectionClass($fixerClass);
        /** @var FixerInterface $fixer */
        $fixer = $fixerReflection->newInstanceWithoutConstructor();

        return $fixer->getName();
    }
}
