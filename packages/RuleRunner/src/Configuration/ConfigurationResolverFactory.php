<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Configuration;

use PhpCsFixer\Config;
use PhpCsFixer\Console\ConfigurationResolver;

final class ConfigurationResolverFactory
{
    public function createFromRulesAndExcludedRules(array $rules, array $excludedRules) : ConfigurationResolver
    {
        $rulesAsString = $this->combineListOfRulesToString($rules, $excludedRules);
        $options = $this->createOptionsWithRules($rulesAsString);

        return new ConfigurationResolver(new Config(), $options, getcwd());
    }

    private function combineListOfRulesToString(array $rules, array $excludedRules) : string
    {
        $rulesAsString = $this->implodeWithPresign($rules) .
            ',' .
            $this->implodeWithPresign($excludedRules, '-');

        return trim($rulesAsString, ',');
    }

    private function implodeWithPresign(array $items, string $presign = '') : string
    {
        if (count($items)) {
            return $presign . implode(',' . $presign, $items);
        }

        return '';
    }

    private function createOptionsWithRules(string $rulesAsString) : array
    {
        return [
            'rules' => $rulesAsString,
            'allow-risky' => 'yes'
        ];
    }
}
