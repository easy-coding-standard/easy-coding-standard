<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Fixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory as NativeFixerFactory;
use PhpCsFixer\RuleSet;
use Symplify\EasyCodingStandard\RuleRunner\Validator\RuleValidator;

final class FixerFactory
{
    /**
     * @var RuleValidator
     */
    private $ruleValidator;

    /**
     * @var NativeFixerFactory
     */
    private $nativeFixerFactory;

    public function __construct(RuleValidator $ruleValidator, NativeFixerFactory $nativeFixerFactory)
    {
        $this->ruleValidator = $ruleValidator;
        $this->nativeFixerFactory = $nativeFixerFactory;
    }

    /**
     * @return FixerInterface[]
     */
    public function createFromEnabledRulesAndExcludedRules(array $enabledRules, array $excludedRules) : array
    {
        if (!count($enabledRules)) {
            return [];
        }

        $ruleSet = $this->createRuleSetFromEnabledAndExcludedRules($enabledRules, $excludedRules);
        $this->nativeFixerFactory->useRuleSet($ruleSet);
        return $this->nativeFixerFactory->getFixers();
    }

    private function createRuleSetFromEnabledAndExcludedRules(array $enabledRules, array $excludedRules) : RuleSet
    {
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

        foreach ($excludedRules as $name) {
            $rules[$name] = false;
        }

        $this->ruleValidator->validateRules($rules);

        return new RuleSet($rules);
    }
}
