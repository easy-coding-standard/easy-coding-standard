<?php

declare (strict_types=1);
namespace ECSPrefix202607\TomasVotruba\ClassLeak\Filtering;

use ECSPrefix202607\TomasVotruba\ClassLeak\ValueObject\FileWithClass;
use ECSPrefix202607\Webmozart\Assert\Assert;
final class PossiblyUnusedClassesFilter
{
    /**
     * These class types are used by some kind of collector pattern. Either loaded magically, registered only in config,
     * an entry point or a tagged extensions.
     *
     * @var string[]
     */
    private const DEFAULT_TYPES_TO_SKIP = [
        // http-kernel
        'ECSPrefix202607\Symfony\Component\Console\Application',
        'ECSPrefix202607\Symfony\Component\HttpKernel\DependencyInjection\Extension',
        'ECSPrefix202607\Symfony\Bundle\FrameworkBundle\Controller\Controller',
        'ECSPrefix202607\Symfony\Bundle\FrameworkBundle\Controller\AbstractController',
        'ECSPrefix202607\Livewire\Component',
        'ECSPrefix202607\Illuminate\Routing\Controller',
        'ECSPrefix202607\Illuminate\Contracts\Http\Kernel',
        'ECSPrefix202607\Illuminate\Support\ServiceProvider',
        // events
        'ECSPrefix202607\Symfony\Component\EventDispatcher\EventSubscriberInterface',
        'ECSPrefix202607\Symfony\Component\Form\FormTypeExtensionInterface',
        'ECSPrefix202607\Symfony\Component\Security\Core\Authentication\SimpleAuthenticatorInterface',
        'ECSPrefix202607\Vich\UploaderBundle\Naming\DirectoryNamerInterface',
        // validator
        'ECSPrefix202607\Symfony\Component\Validator\Constraint',
        'ECSPrefix202607\Symfony\Component\Validator\ConstraintValidator',
        'ECSPrefix202607\Symfony\Component\Validator\ConstraintValidatorInterface',
        'ECSPrefix202607\Symfony\Component\Security\Core\Authorization\Voter\VoterInterface',
        'ECSPrefix202607\Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface',
        'ECSPrefix202607\Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface',
        'ECSPrefix202607\Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface',
        'ECSPrefix202607\Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface',
        // symfony forms
        'ECSPrefix202607\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface',
        'ECSPrefix202607\Symfony\Component\Form\AbstractType',
        // doctrine
        'ECSPrefix202607\Doctrine\Common\DataFixtures\FixtureInterface',
        'ECSPrefix202607\Doctrine\Common\EventSubscriber',
        'ECSPrefix202607\Nelmio\Alice\ProcessorInterface',
        // kernel
        'ECSPrefix202607\Symfony\Component\HttpKernel\Bundle\BundleInterface',
        'ECSPrefix202607\Symfony\Component\HttpKernel\KernelInterface',
        'ECSPrefix202607\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
        // console
        'ECSPrefix202607\Symfony\Component\Console\Command\Command',
        'ECSPrefix202607\Entropy\Console\Contract\CommandInterface',
        'ECSPrefix202607\Twig\Extension\ExtensionInterface',
        'PhpCsFixer\Fixer\FixerInterface',
        'PHPUnit\Framework\TestCase',
        'ECSPrefix202607\PHPStan\Rules\Rule',
        'ECSPrefix202607\PHPStan\Command\ErrorFormatter\ErrorFormatter',
        // tests
        'ECSPrefix202607\Behat\Behat\Context\Context',
        // jms
        'ECSPrefix202607\JMS\Serializer\Handler\SubscribingHandlerInterface',
        // laravel
        'ECSPrefix202607\Illuminate\Support\ServiceProvider',
        'ECSPrefix202607\Illuminate\Foundation\Http\Kernel',
        'ECSPrefix202607\Illuminate\Contracts\Console\Kernel',
        'ECSPrefix202607\Illuminate\Routing\Controller',
        // Doctrine
        'ECSPrefix202607\Doctrine\Migrations\AbstractMigration',
    ];
    /**
     * @var string[]
     */
    private const DEFAULT_ATTRIBUTES_TO_SKIP = [
        // Symfony
        'ECSPrefix202607\Symfony\Component\Console\Attribute\AsCommand',
        'ECSPrefix202607\Symfony\Component\HttpKernel\Attribute\AsController',
        'ECSPrefix202607\Symfony\Component\EventDispatcher\Attribute\AsEventListener',
    ];
    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedClassNames
     * @param string[] $typesToSkip
     * @param string[] $suffixesToSkip
     * @param string[] $attributesToSkip
     *
     * @return FileWithClass[]
     */
    public function filter(array $filesWithClasses, array $usedClassNames, array $typesToSkip, array $suffixesToSkip, array $attributesToSkip, bool $shouldIncludeEntities): array
    {
        Assert::allString($usedClassNames);
        Assert::allString($typesToSkip);
        Assert::allString($suffixesToSkip);
        $possiblyUnusedFilesWithClasses = [];
        $typesToSkip = array_merge($typesToSkip, self::DEFAULT_TYPES_TO_SKIP);
        $attributesToSkip = array_merge($attributesToSkip, self::DEFAULT_ATTRIBUTES_TO_SKIP);
        foreach ($filesWithClasses as $fileWithClass) {
            if (in_array($fileWithClass->getClassName(), $usedClassNames, \true)) {
                continue;
            }
            // is excluded interfaces?
            if ($this->shouldSkip($fileWithClass->getClassName(), $typesToSkip)) {
                continue;
            }
            if ($shouldIncludeEntities === \false && $fileWithClass->isEntity()) {
                continue;
            }
            if ($fileWithClass->isSerialized()) {
                continue;
            }
            // is excluded suffix?
            foreach ($suffixesToSkip as $suffixToSkip) {
                if (substr_compare($fileWithClass->getClassName(), $suffixToSkip, -strlen($suffixToSkip)) === 0) {
                    continue 2;
                }
            }
            // is excluded attributes?
            foreach ($fileWithClass->getAttributes() as $attribute) {
                if ($this->shouldSkip($attribute, $attributesToSkip)) {
                    continue 2;
                }
            }
            $possiblyUnusedFilesWithClasses[] = $fileWithClass;
        }
        return $possiblyUnusedFilesWithClasses;
    }
    /**
     * @param string[] $skips
     */
    private function shouldSkip(string $type, array $skips): bool
    {
        foreach ($skips as $skip) {
            if (strpos($type, '*') === \false && is_a($type, $skip, \true)) {
                return \true;
            }
            if (fnmatch($skip, $type, \FNM_NOESCAPE)) {
                return \true;
            }
        }
        return \false;
    }
}
