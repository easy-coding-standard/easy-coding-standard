<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\PhpConfigPrinter\RoutingCaseConverter;

use ConfigTransformer20210601\PhpParser\Node\Arg;
use ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall;
use ConfigTransformer20210601\PhpParser\Node\Expr\Variable;
use ConfigTransformer20210601\PhpParser\Node\Stmt\Expression;
use ConfigTransformer20210601\Symplify\PackageBuilder\Strings\StringFormatConverter;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\RoutingCaseConverterInterface;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\VariableName;
final class ImportRoutingCaseConverter implements \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\RoutingCaseConverterInterface
{
    /**
     * @var string[]
     */
    const NESTED_KEYS = ['name_prefix', 'defaults', 'requirements', 'options', 'utf8', 'condition', 'host', 'schemes', self::METHODS, 'controller', 'locale', 'format', 'stateless'];
    /**
     * @var string[]
     */
    const IMPORT_ARGS = [self::RESOURCE, self::TYPE, self::EXCLUDE];
    /**
     * @var string[]
     */
    const PREFIX_ARGS = [
        // Add prefix itself as first argument
        self::PREFIX,
        'trailing_slash_on_root',
    ];
    /**
     * @var string
     */
    const PREFIX = 'prefix';
    /**
     * @var string
     */
    const RESOURCE = 'resource';
    /**
     * @var string
     */
    const TYPE = 'type';
    /**
     * @var string
     */
    const EXCLUDE = 'exclude';
    /**
     * @var string
     */
    const METHODS = 'methods';
    /**
     * @var ArgsNodeFactory
     */
    private $argsNodeFactory;
    /**
     * @var StringFormatConverter
     */
    private $stringFormatConverter;
    public function __construct(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory $argsNodeFactory)
    {
        $this->argsNodeFactory = $argsNodeFactory;
        $this->stringFormatConverter = new \ConfigTransformer20210601\Symplify\PackageBuilder\Strings\StringFormatConverter();
    }
    public function match(string $key, $values) : bool
    {
        return isset($values[self::RESOURCE]);
    }
    public function convertToMethodCall(string $key, $values) : \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression
    {
        $variable = new \ConfigTransformer20210601\PhpParser\Node\Expr\Variable(\ConfigTransformer20210601\Symplify\PhpConfigPrinter\ValueObject\VariableName::ROUTING_CONFIGURATOR);
        $args = $this->createAddArgs(self::IMPORT_ARGS, $values);
        $methodCall = new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($variable, 'import', $args);
        // Handle prefix independently as it has specific args
        if (isset($values[self::PREFIX])) {
            $args = $this->createAddArgs(self::PREFIX_ARGS, $values);
            $methodCall = new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($methodCall, self::PREFIX, $args);
        }
        foreach (self::NESTED_KEYS as $nestedKey) {
            if (!isset($values[$nestedKey])) {
                continue;
            }
            $nestedValues = $values[$nestedKey];
            // Transform methods as string GET|HEAD to array
            if ($nestedKey === self::METHODS && \is_string($nestedValues)) {
                $nestedValues = \explode('|', $nestedValues);
            }
            $args = $this->argsNodeFactory->createFromValues([$nestedValues]);
            $name = $this->stringFormatConverter->underscoreAndHyphenToCamelCase($nestedKey);
            $methodCall = new \ConfigTransformer20210601\PhpParser\Node\Expr\MethodCall($methodCall, $name, $args);
        }
        return new \ConfigTransformer20210601\PhpParser\Node\Stmt\Expression($methodCall);
    }
    /**
     * @param string[] $argsNames
     * @param mixed $values
     * @return Arg[]
     */
    private function createAddArgs(array $argsNames, $values) : array
    {
        $argumentValues = [];
        foreach ($argsNames as $arg) {
            if (isset($values[$arg])) {
                // Default $ignoreErrors to false before $exclude on import(), same behaviour as symfony
                if ($arg === self::EXCLUDE) {
                    $argumentValues[] = \false;
                }
                $argumentValues[] = $values[$arg];
            }
        }
        return $this->argsNodeFactory->createFromValues($argumentValues);
    }
}
