<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors;

use ECSPrefix202609\Doctrine\Common\EventSubscriber;
use ECSPrefix202609\Illuminate\Console\Command;
use ECSPrefix202609\JMS\Serializer\Handler\SubscribingHandlerInterface;
use ECSPrefix202609\Livewire\Component;
use ECSPrefix202609\PhpParser\Node;
use ECSPrefix202609\PhpParser\Node\Stmt\ClassMethod;
use ECSPrefix202609\PHPStan\Analyser\Scope;
use ECSPrefix202609\PHPStan\Collectors\Collector;
use ECSPrefix202609\PHPStan\Reflection\ClassReflection;
use ECSPrefix202609\Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ECSPrefix202609\TomasVotruba\UnusedPublic\ApiDocStmtAnalyzer;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Configuration;
use ECSPrefix202609\TomasVotruba\UnusedPublic\MethodTypeDetector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\PublicClassMethodMatcher;
use ECSPrefix202609\Twig\Extension\ExtensionInterface;
/**
 * @implements Collector<ClassMethod, array{class-string, string, int}>
 */
final class PublicClassMethodCollector implements Collector
{
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\ApiDocStmtAnalyzer
     */
    private $apiDocStmtAnalyzer;
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\PublicClassMethodMatcher
     */
    private $publicClassMethodMatcher;
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\MethodTypeDetector
     */
    private $methodTypeDetector;
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\Configuration
     */
    private $configuration;
    /**
     * @var string[]
     */
    private const SKIPPED_TYPES = [
        // symfony
        'ECSPrefix202609\Symfony\Component\EventDispatcher\EventSubscriberInterface',
        // doctrine
        EventSubscriber::class,
        SubscribingHandlerInterface::class,
        ExtensionInterface::class,
        Controller::class,
        // laravel
        Command::class,
        Component::class,
        'ECSPrefix202609\Illuminate\Http\Request',
        'ECSPrefix202609\Illuminate\Contracts\Mail\Mailable',
        'ECSPrefix202609\Illuminate\Contracts\Queue\ShouldQueue',
        'ECSPrefix202609\Illuminate\Support\ServiceProvider',
    ];
    public function __construct(ApiDocStmtAnalyzer $apiDocStmtAnalyzer, PublicClassMethodMatcher $publicClassMethodMatcher, MethodTypeDetector $methodTypeDetector, Configuration $configuration)
    {
        $this->apiDocStmtAnalyzer = $apiDocStmtAnalyzer;
        $this->publicClassMethodMatcher = $publicClassMethodMatcher;
        $this->methodTypeDetector = $methodTypeDetector;
        $this->configuration = $configuration;
    }
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }
    /**
     * @param ClassMethod $node
     * @return array{class-string, string, int}|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (!$this->configuration->shouldCollectMethods()) {
            return null;
        }
        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            return null;
        }
        if ($this->shouldSkip($classReflection, $node, $scope)) {
            return null;
        }
        if ($this->publicClassMethodMatcher->shouldSkipClassMethod($node)) {
            return null;
        }
        if ($this->apiDocStmtAnalyzer->isApiDoc($node, $classReflection)) {
            return null;
        }
        if ($this->isSkippedType($classReflection)) {
            return null;
        }
        if ($this->publicClassMethodMatcher->shouldSkipClassReflection($classReflection)) {
            return null;
        }
        $methodName = $node->name->toString();
        // is this method required by parent contract? skip it
        if ($this->publicClassMethodMatcher->isUsedByParentClassOrInterface($classReflection, $methodName)) {
            return null;
        }
        return [$classReflection->getName(), $methodName, $node->getLine()];
    }
    private function shouldSkip(ClassReflection $classReflection, ClassMethod $classMethod, Scope $scope): bool
    {
        // skip acceptance tests, codeception
        if (substr_compare($classReflection->getName(), 'Cest', -strlen('Cest')) === 0) {
            return \true;
        }
        if ($this->methodTypeDetector->isTestMethod($classMethod, $scope)) {
            return \true;
        }
        return $this->methodTypeDetector->isTraitMethod($classMethod, $scope);
    }
    private function isSkippedType(ClassReflection $classReflection): bool
    {
        $found = \false;
        foreach (self::SKIPPED_TYPES as $skippedType) {
            if ($classReflection->isSubclassOf($skippedType)) {
                $found = \true;
                break;
            }
        }
        return $found;
    }
}
