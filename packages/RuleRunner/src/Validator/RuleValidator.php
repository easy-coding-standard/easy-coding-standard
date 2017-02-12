<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Validator;

use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;

/**
 * @note extracted from https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/ef9ab2ed1f666d75c9ed827b399e43f88bac7298/src/Console/ConfigurationResolver.php#L619-L654
 */
final class RuleValidator
{
    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    public function __construct(FixerFactory $fixerFactory)
    {
        $this->fixerFactory = $fixerFactory;
    }

    public function validateRules(array $rules)
    {
//        /**
//         * Create a ruleset that contains all configured rules, even when they originally have been disabled.
//         *
//         * @see RuleSet::resolveSet()
//         */
//        $ruleSet = RuleSet::create(array_map(function () {
//            return true;
//        }, $rules));

        $ruleSet = RuleSet::create($rules);

        $configuredFixers = array_keys($ruleSet->getRules());

        $availableFixers = array_map(function (FixerInterface $fixer) {
            return $fixer->getName();
        }, $this->fixerFactory->getFixers());

        $unknownFixers = array_diff($configuredFixers, $availableFixers);

        if (count($unknownFixers)) {
            throw new InvalidConfigurationException(sprintf(
                'The rules contain unknown fixers (%s).',
                implode(', ', $unknownFixers)
            ));
            // @todo: suggest correct one, throw per value!, not diff
        }
    }
}
