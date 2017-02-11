<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Sniff\Factory;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\SniffRunner\Exception\ClassNotFoundException;

final class SniffFactory
{
    /**
     * @var array[][] { sniffClass => { property => value }}
     */
    private $sniffPropertyValues;

    public function setSniffPropertyValues(array $sniffPropertyValues)
    {
        $this->sniffPropertyValues = $sniffPropertyValues;
    }

    public function create(string $sniffClass) : Sniff
    {
        $this->ensureSniffClassExists($sniffClass);

        $sniff = new $sniffClass;
        $this->decorateSniffWithValues($sniff, $sniffClass);
        return $sniff;
    }

    private function decorateSniffWithValues(Sniff $sniff, string $sniffClass) : void
    {
        if (!isset($sniffPropertyValues[$sniffClass])) {
            return;
        }

        foreach ($sniffPropertyValues[$sniffClass] as $property => $value) {
            $sniff->$property = $value;
        }
    }

    private function ensureSniffClassExists(string $sniffClass) : void
    {
        if (!class_exists($sniffClass)) {
            throw new ClassNotFoundException(sprintf(
                "Sniff class '%s' was not found.", $sniffClass
            ));
        }
    }
}
