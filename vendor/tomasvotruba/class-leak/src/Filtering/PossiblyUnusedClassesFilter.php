<?php

declare (strict_types=1);
namespace ECSPrefix202606\TomasVotruba\ClassLeak\Filtering;

use ECSPrefix202606\TomasVotruba\ClassLeak\ValueObject\FileWithClass;
use ECSPrefix202606\Webmozart\Assert\Assert;
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
        'ECSPrefix202606\Symfony\Component\Console\Application',
        'ECSPrefix202606\Symfony\Component\HttpKernel\DependencyInjection\Extension',
        'ECSPrefix202606\Symfony\Bundle\FrameworkBundle\Controller\Controller',
        'ECSPrefix202606\Symfony\Bundle\FrameworkBundle\Controller\AbstractController',
        'ECSPrefix202606\Livewire\Component',
        'ECSPrefix202606\Illuminate\Routing\Controller',
        'ECSPrefix202606\Illuminate\Contracts\Http\Kernel',
        'ECSPrefix202606\Illuminate\Support\ServiceProvider',
        // events
        'ECSPrefix202606\Symfony\Component\EventDispatcher\EventSubscriberInterface',
        'ECSPrefix202606\Symfony\Component\Form\FormTypeExtensionInterface',
        'ECSPrefix202606\Symfony\Component\Security\Core\Authentication\SimpleAuthenticatorInterface',
        'ECSPrefix202606\Vich\UploaderBundle\Naming\DirectoryNamerInterface',
        // validator
        'ECSPrefix202606\Symfony\Component\Validator\Constraint',
        'ECSPrefix202606\Symfony\Component\Validator\ConstraintValidator',
        'ECSPrefix202606\Symfony\Component\Validator\ConstraintValidatorInterface',
        'ECSPrefix202606\Symfony\Component\Security\Core\Authorization\Voter\VoterInterface',
        'ECSPrefix202606\Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface',
        'ECSPrefix202606\Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface',
        'ECSPrefix202606\Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface',
        'ECSPrefix202606\Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface',
        // symfony forms
        'ECSPrefix202606\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface',
        'ECSPrefix202606\Symfony\Component\Form\AbstractType',
        // doctrine
        'ECSPrefix202606\Doctrine\Common\DataFixtures\FixtureInterface',
        'ECSPrefix202606\Doctrine\Common\EventSubscriber',
        'ECSPrefix202606\Nelmio\Alice\ProcessorInterface',
        // kernel
        'ECSPrefix202606\Symfony\Component\HttpKernel\Bundle\BundleInterface',
        'ECSPrefix202606\Symfony\Component\HttpKernel\KernelInterface',
        'ECSPrefix202606\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
        // console
        'ECSPrefix202606\Symfony\Component\Console\Command\Command',
        'ECSPrefix202606\Entropy\Console\Contract\CommandInterface',
        'ECSPrefix202606\Twig\Extension\ExtensionInterface',
        'PhpCsFixer\Fixer\FixerInterface',
        'PHPUnit\Framework\TestCase',
        'ECSPrefix202606\PHPStan\Rules\Rule',
        'ECSPrefix202606\PHPStan\Command\ErrorFormatter\ErrorFormatter',
        // tests
        'ECSPrefix202606\Behat\Behat\Context\Context',
        // jms
        'ECSPrefix202606\JMS\Serializer\Handler\SubscribingHandlerInterface',
        // laravel
        'ECSPrefix202606\Illuminate\Support\ServiceProvider',
        'ECSPrefix202606\Illuminate\Foundation\Http\Kernel',
        'ECSPrefix202606\Illuminate\Contracts\Console\Kernel',
        'ECSPrefix202606\Illuminate\Routing\Controller',
        // Doctrine
        'ECSPrefix202606\Doctrine\Migrations\AbstractMigration',
    ];
    /**
     * @var string[]
     */
    private const DEFAULT_ATTRIBUTES_TO_SKIP = [
        // Symfony
        'ECSPrefix202606\Symfony\Component\Console\Attribute\AsCommand',
        'ECSPrefix202606\Symfony\Component\HttpKernel\Attribute\AsController',
        'ECSPrefix202606\Symfony\Component\EventDispatcher\Attribute\AsEventListener',
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
