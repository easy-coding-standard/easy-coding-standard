<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Fixer;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;

final class FixerFactory
{
    /**
     * @return FixerInterface[]
     */
    public function createFromFixerClasses(array $fixerClasses) : array
    {
        $fixers = [];

        $rules = [];
        foreach ($fixerClasses as $name => $rule) {
            if (is_array($rule)) {
                $config = $rule;
                $rules[$name] = $config;
            } else {
                $name = $rule;
                $rules[$name] = true;
            }
        }

        foreach ($rules as $class => $config) {
            $fixer = new $class;
            $this->configureFixer($fixer, $config);
            $fixers[] = $fixer;
        }

        return $fixers;
    }

    /**
     * @param FixerInterface $fixer
     * @param array|bool $config
     */
    private function configureFixer(FixerInterface $fixer, $config): void
    {
        if ($fixer instanceof ConfigurableFixerInterface) {
            if (is_array($config)) {
                $fixer->configure($config);
            }
        }
    }
}
