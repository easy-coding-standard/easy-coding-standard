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

    public function validateRules(array $rules) : void
    {
        $usedFixers = array_keys(RuleSet::create($rules)->getRules());
        $availableFixers = $this->getAvailableFixers();

        foreach ($usedFixers as $usedFixer) {
            if ( !in_array($usedFixer, $availableFixers, true)) {
                throw new InvalidConfigurationException($this->createMessage($usedFixer, $availableFixers));
            }
        }
    }

    private function getAvailableFixers() : array
    {
        return array_map(function (FixerInterface $fixer) {
            return $fixer->getName();
        }, $this->nativeFixerFactory->getFixers());
    }

    private function createMessage(string $usedFixer, array $availableFixers) : string
    {
        $message = sprintf('The rule "%s" was not found.', $usedFixer);

        $suggestion = ObjectMixin::getSuggestion($availableFixers, $usedFixer);
        if ($suggestion) {
            $message .= sprintf(' Did you mean "%s"?', $suggestion);
        }

        return $message;
    }
}
