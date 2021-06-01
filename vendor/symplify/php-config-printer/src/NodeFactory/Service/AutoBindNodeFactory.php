<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\Service;

use ConfigTransformer20210601\PhpParser\Node\Arg;
use ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Converter\ServiceOptionsKeyYamlToPhpFactory\TagsServiceOptionKeyYamlToPhpFactory;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\SymfonyVersionFeature;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey;
final class AutoBindNodeFactory
{
    /**
     * @var string
     */
    const TYPE_SERVICE = 'service';
    /**
     * @var string
     */
    const TYPE_DEFAULTS = 'defaults';
    /**
     * @var CommonNodeFactory
     */
    private $commonNodeFactory;
    /**
     * @var ArgsNodeFactory
     */
    private $argsNodeFactory;
    /**
     * @var SymfonyVersionFeatureGuardInterface
     */
    private $symfonyVersionFeatureGuard;
    /**
     * @var TagsServiceOptionKeyYamlToPhpFactory
     */
    private $tagsServiceOptionKeyYamlToPhpFactory;
    public function __construct(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory $commonNodeFactory, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory $argsNodeFactory, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface $symfonyVersionFeatureGuard, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Converter\ServiceOptionsKeyYamlToPhpFactory\TagsServiceOptionKeyYamlToPhpFactory $tagsServiceOptionKeyYamlToPhpFactory)
    {
        $this->commonNodeFactory = $commonNodeFactory;
        $this->argsNodeFactory = $argsNodeFactory;
        $this->symfonyVersionFeatureGuard = $symfonyVersionFeatureGuard;
        $this->tagsServiceOptionKeyYamlToPhpFactory = $tagsServiceOptionKeyYamlToPhpFactory;
    }
    /**
     * Decorated node with:
     * ->autowire()
     * ->autoconfigure()
     * ->bind()
     */
    public function createAutoBindCalls(array $yaml, \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall $methodCall, string $type) : \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall
    {
        foreach ($yaml as $key => $value) {
            if ($key === \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::AUTOWIRE) {
                $methodCall = $this->createAutowire($value, $methodCall, $type);
            }
            if ($key === \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::AUTOCONFIGURE) {
                $methodCall = $this->createAutoconfigure($value, $methodCall, $type);
            }
            if ($key === \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::PUBLIC) {
                $methodCall = $this->createPublicPrivate($value, $methodCall, $type);
            }
            if ($key === \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::BIND) {
                $methodCall = $this->createBindMethodCall($methodCall, $yaml[\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::BIND]);
            }
            if ($key === \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::TAGS) {
                $methodCall = $this->createTagsMethodCall($methodCall, $value);
            }
        }
        return $methodCall;
    }
    private function createBindMethodCall(\ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall $methodCall, array $bindValues) : \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall
    {
        foreach ($bindValues as $key => $value) {
            $args = $this->argsNodeFactory->createFromValues([$key, $value]);
            $methodCall = new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($methodCall, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::BIND, $args);
        }
        return $methodCall;
    }
    private function createAutowire($value, \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall $methodCall, string $type) : \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall
    {
        if ($value === \true) {
            return new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($methodCall, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::AUTOWIRE);
        }
        // skip default false
        if ($type === self::TYPE_DEFAULTS) {
            return $methodCall;
        }
        $args = [new \ConfigTransformer20210601\PhpParser\Node\Arg($this->commonNodeFactory->createFalse())];
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($methodCall, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::AUTOWIRE, $args);
    }
    private function createAutoconfigure($value, \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall $methodCall, string $type) : \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall
    {
        if ($value === \true) {
            return new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($methodCall, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::AUTOCONFIGURE);
        }
        // skip default false
        if ($type === self::TYPE_DEFAULTS) {
            return $methodCall;
        }
        $args = [new \ConfigTransformer20210601\PhpParser\Node\Arg($this->commonNodeFactory->createFalse())];
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($methodCall, \ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\YamlKey::AUTOCONFIGURE, $args);
    }
    private function createPublicPrivate($value, \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall $methodCall, string $type) : \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall
    {
        if ($value !== \false) {
            return new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($methodCall, 'public');
        }
        // default value
        if ($type === self::TYPE_DEFAULTS) {
            if ($this->symfonyVersionFeatureGuard->isAtLeastSymfonyVersion(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\SymfonyVersionFeature::PRIVATE_SERVICES_BY_DEFAULT)) {
                return $methodCall;
            }
            return new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($methodCall, 'private');
        }
        $args = [new \ConfigTransformer20210601\PhpParser\Node\Arg($this->commonNodeFactory->createFalse())];
        return new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($methodCall, 'public', $args);
    }
    private function createTagsMethodCall(\ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall $methodCall, $value) : \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall
    {
        return $this->tagsServiceOptionKeyYamlToPhpFactory->decorateServiceMethodCall(null, $value, [], $methodCall);
    }
}
