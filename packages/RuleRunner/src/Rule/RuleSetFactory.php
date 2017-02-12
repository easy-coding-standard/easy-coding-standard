<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Rule;

use PhpCsFixer\RuleSet;
use Symplify\EasyCodingStandard\RuleRunner\Validator\RuleValidator;

final class RuleSetFactory
{
    /**
     * @var RuleValidator
     */
    private $ruleValidator;

    public function __construct(RuleValidator $ruleValidator)
    {
        $this->ruleValidator = $ruleValidator;
    }

    public function createFromEnabledAndExcludedRules(array $enabledRules, array $excludedRules) : RuleSet
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
