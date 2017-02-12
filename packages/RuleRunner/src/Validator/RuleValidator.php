<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Validator;

use Nette\Utils\ObjectMixin;
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
    private $nativeFixerFactory;

    public function __construct(FixerFactory $nativeFixerFactory)
    {
        $this->nativeFixerFactory = $nativeFixerFactory;
    }

    public function validateRules(array $rules)
    {
        $usedFixers = array_keys(RuleSet::create($rules)->getRules());

        $availableFixers = $this->getAvailableFixers();

        foreach ($usedFixers as $usedFixer) {
            if ( !in_array($usedFixer, $availableFixers)) {
                $suggestion = ObjectMixin::getSuggestion($availableFixers, $usedFixer);
                throw new InvalidConfigurationException(sprintf(
                    'The rule "%s" was not found. Did you mean "%s"?',
                    $usedFixer,
                    $suggestion
                ));
            }
        }
    }

    /**
     * @return string[]
     */
    private function getAvailableFixers(): array
    {
        return array_map(function (FixerInterface $fixer) {
            return $fixer->getName();
        }, $this->nativeFixerFactory->getFixers());
    }
}
