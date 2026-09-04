<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\Rules;

use ECSPrefix202609\PhpParser\Node;
use ECSPrefix202609\PHPStan\Analyser\Scope;
use ECSPrefix202609\PHPStan\Node\CollectedDataNode;
use ECSPrefix202609\PHPStan\Rules\Rule;
use ECSPrefix202609\PHPStan\Rules\RuleError;
use ECSPrefix202609\PHPStan\Rules\RuleErrorBuilder;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors\ClassConstFetchCollector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Collectors\PublicClassLikeConstCollector;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Configuration;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Enum\RuleTips;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Enum\Template\BladeRegex;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Templates\TemplateRegexFinder;
use ECSPrefix202609\TomasVotruba\UnusedPublic\Utils\Arrays;
/**
 * @see \TomasVotruba\UnusedPublic\Tests\Rules\UnusedPublicClassConstRule\UnusedPublicClassConstRuleTest
 */
final class UnusedPublicClassConstRule implements Rule
{
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\Configuration
     */
    private $configuration;
    /**
     * @readonly
     * @var \TomasVotruba\UnusedPublic\Templates\TemplateRegexFinder
     */
    private $templateRegexFinder;
    /**
     * @api
     * @var string
     */
    public const ERROR_MESSAGE = 'Public constant "%s::%s" is never used';
    public function __construct(Configuration $configuration, TemplateRegexFinder $templateRegexFinder)
    {
        $this->configuration = $configuration;
        $this->templateRegexFinder = $templateRegexFinder;
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
        if (!$this->configuration->isUnusedConstantsEnabled()) {
            return [];
        }
        $bladeConstFetchNames = $this->templateRegexFinder->find($this->configuration->getTemplatePaths(), 'blade.php', [BladeRegex::INNER_REGEX, BladeRegex::TAG_REGEX], BladeRegex::CONSTANT_FETCH_REGEX);
        $classConstFetchCollector = $node->get(ClassConstFetchCollector::class);
        $publicClassLikeConstCollector = $node->get(PublicClassLikeConstCollector::class);
        $usedConstFetches = Arrays::flatten($classConstFetchCollector);
        $ruleErrors = [];
        foreach ($publicClassLikeConstCollector as $filePath => $declarationsGroups) {
            foreach ($declarationsGroups as $declarationGroup) {
                if ($declarationGroup === null) {
                    continue;
                }
                foreach ($declarationGroup as [$className, $constantName, $line]) {
                    if ($this->isClassConstantUsed($className, $constantName, $usedConstFetches, $bladeConstFetchNames)) {
                        continue;
                    }
                    /** @var string $constantName */
                    $errorMessage = sprintf(self::ERROR_MESSAGE, $className, $constantName);
                    $ruleErrors[] = RuleErrorBuilder::message($errorMessage)->file($filePath)->line($line)->tip(RuleTips::SOLUTION_MESSAGE)->identifier('public.classConstant.unused')->build();
                }
            }
        }
        return $ruleErrors;
    }
    /**
     * @param mixed[] $usedConstFetches
     * @param string[] $bladeConstFetchNames
     */
    private function isClassConstantUsed(string $className, string $constantName, array $usedConstFetches, array $bladeConstFetchNames): bool
    {
        // used in template
        if (in_array($constantName, $bladeConstFetchNames, \true)) {
            return \true;
        }
        $publicConstantReference = $className . '::' . $constantName;
        return in_array($publicConstantReference, $usedConstFetches, \true);
    }
}
