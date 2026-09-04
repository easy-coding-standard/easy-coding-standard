<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\Rules;

use ECSPrefix202609\PhpParser\Node;
use ECSPrefix202609\PHPStan\Analyser\Scope;
use ECSPrefix202609\PHPStan\Node\CollectedDataNode;
use ECSPrefix202609\PHPStan\Rules\Rule;
use ECSPrefix202609\PHPStan\Rules\RuleError;
use ECSPrefix202609\PHPStan\Rules\RuleErrorBuilder;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors\PublicClassMethodCollector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Configuration;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Enum\RuleTips;
use ECSPrefix202609\TomasVotruba\UnusedPublic\NodeCollectorExtractor;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Templates\TemplateMethodCallsProvider;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Templates\UsedMethodAnalyzer;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Utils\Strings;
/**
 * @see \TomasVotruba\UnusedPublic\Tests\Rules\LocalOnlyPublicClassMethodRule\LocalOnlyPublicClassMethodRuleTest
 */
final class LocalOnlyPublicClassMethodRule implements Rule
{
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\Configuration
     */
    private $configuration;
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\Templates\UsedMethodAnalyzer
     */
    private $usedMethodAnalyzer;
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\Templates\TemplateMethodCallsProvider
     */
    private $templateMethodCallsProvider;
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\NodeCollectorExtractor
     */
    private $nodeCollectorExtractor;
    /**
     * @api
     * @var string
     */
    public const ERROR_MESSAGE = 'Public method "%s::%s()" is used only locally and should be turned protected/private';
    public function __construct(Configuration $configuration, UsedMethodAnalyzer $usedMethodAnalyzer, TemplateMethodCallsProvider $templateMethodCallsProvider, NodeCollectorExtractor $nodeCollectorExtractor)
    {
        $this->configuration = $configuration;
        $this->usedMethodAnalyzer = $usedMethodAnalyzer;
        $this->templateMethodCallsProvider = $templateMethodCallsProvider;
        $this->nodeCollectorExtractor = $nodeCollectorExtractor;
    }
    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }
    /**
     * @param CollectedDataNode $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$this->configuration->isLocalMethodEnabled()) {
            return [];
        }
        $twigMethodNames = $this->templateMethodCallsProvider->provideTwigMethodCalls();
        $localAndExternalMethodCallReferences = $this->nodeCollectorExtractor->extractLocalAndExternalMethodCallReferences($node);
        // php method calls are case-insensitive
        $lowerExternalRefs = Strings::lowercase($localAndExternalMethodCallReferences->getExternalMethodCallReferences());
        $lowerLocalRefs = Strings::lowercase($localAndExternalMethodCallReferences->getLocalMethodCallReferences());
        $ruleErrors = [];
        $publicClassMethodCollector = $node->get(PublicClassMethodCollector::class);
        foreach ($publicClassMethodCollector as $filePath => $declarations) {
            foreach ($declarations as [$className, $methodName, $line]) {
                if (!$this->isUsedOnlyLocally($className, $methodName, $lowerExternalRefs, $lowerLocalRefs, $twigMethodNames)) {
                    continue;
                }
                /** @var string $methodName */
                $errorMessage = sprintf(self::ERROR_MESSAGE, $className, $methodName);
                $ruleErrors[] = RuleErrorBuilder::message($errorMessage)->file($filePath)->line($line)->tip(RuleTips::NARROW_SCOPE)->identifier('public.method.unused')->build();
            }
        }
        return $ruleErrors;
    }
    /**
     * @param string[] $lowerExternalRefs
     * @param string[] $lowerLocalRefs
     * @param string[] $twigMethodNames
     */
    private function isUsedOnlyLocally(string $className, string $methodName, array $lowerExternalRefs, array $lowerLocalRefs, array $twigMethodNames): bool
    {
        if ($this->usedMethodAnalyzer->isUsedInTwig($methodName, $twigMethodNames)) {
            return \true;
        }
        $publicMethodReference = strtolower($className . '::' . $methodName);
        if (in_array($publicMethodReference, $lowerExternalRefs, \true)) {
            return \false;
        }
        return in_array($publicMethodReference, $lowerLocalRefs, \true);
    }
}
