<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Fixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory as NativeFixerFactory;
use Symplify\EasyCodingStandard\RuleRunner\Rule\RuleSetFactory;

final class FixerFactory
{
    /**
     * @var NativeFixerFactory
     */
    private $nativeFixerFactory;

    /**
     * @var RuleSetFactory
     */
    private $ruleSetFactory;

    public function __construct(NativeFixerFactory $nativeFixerFactory, RuleSetFactory $ruleSetFactory)
    {
        $this->nativeFixerFactory = $nativeFixerFactory;
        $this->ruleSetFactory = $ruleSetFactory;
    }

    /**
     * @return FixerInterface[]
     */
    public function createFromEnabledAndExcludedRules(array $enabledRules, array $excludedRules) : array
    {
        if (!count($enabledRules)) {
            return [];
        }

        $ruleSet = $this->ruleSetFactory->createFromEnabledAndExcludedRules($enabledRules, $excludedRules);

        $this->nativeFixerFactory->useRuleSet($ruleSet);

        return $this->nativeFixerFactory->getFixers();
    }
}
