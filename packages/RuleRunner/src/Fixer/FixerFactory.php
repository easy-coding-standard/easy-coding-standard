<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Fixer;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;

final class FixerFactory
{
    /**
     * @return FixerInterface[]
     */
    public function createFromFixerClasses(array $enabledRules) : array
    {
        $fixers = [];

        $rules = [];
        foreach ($enabledRules as $name => $rule) {
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
            if ($fixer instanceof ConfigurableFixerInterface) {
                if (is_array($config)) {
                    $fixer->configure($config);
                }
            }
            $fixers[] = $fixer;
        }

        return $fixers;
    }
}
