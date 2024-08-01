<?php

declare (strict_types=1);
namespace ECSPrefix202408;

use PhpCsFixer\RuleSet\RuleSets;
// this helper script generates the withPhpCsFixerSets() method for ECSConfigBuilder class
require __DIR__ . '/../vendor/autoload.php';
$setsDirectory = __DIR__ . '/../vendor/friendsofphp/php-cs-fixer/src/RuleSet/Sets/';
$setDefinitions = RuleSets::getSetDefinitions();
$setNames = [];
foreach ($setDefinitions as $setDefinition) {
    $setNames[] = $setDefinition->getName();
}
// create withPhpCsFixerSets() method here
$classMethod = new \ECSPrefix202408\PhpParser\Node\Stmt\ClassMethod('withPhpCsFixerSets');
$classMethod->flags = \ECSPrefix202408\PhpParser\Node\Stmt\Class_::MODIFIER_PUBLIC;
$classMethod->returnType = new \ECSPrefix202408\PhpParser\Node\Name('self');
foreach ($setNames as $setName) {
    // convert to PHP variable name
    $paramName = \ltrim($setName, '@');
    $paramName = lowercaseUntilFirstLower($paramName);
    $paramName = \str_replace(':r', 'R', $paramName);
    $paramName = \str_replace(['.', '-', '_'], '', $paramName);
    // lowercase only the first uppercase letters
    $classMethod->params[] = new \ECSPrefix202408\PhpParser\Node\Param(new \ECSPrefix202408\PhpParser\Node\Expr\Variable($paramName), new \ECSPrefix202408\PhpParser\Node\Expr\ConstFetch(new \ECSPrefix202408\PhpParser\Node\Name('false')), new \ECSPrefix202408\PhpParser\Node\Identifier('bool'));
    $dynamicSetsPropertyFetch = new \ECSPrefix202408\PhpParser\Node\Expr\PropertyFetch(new \ECSPrefix202408\PhpParser\Node\Expr\Variable('this'), 'dynamicSets');
    $classMethod->stmts[] = new \ECSPrefix202408\PhpParser\Node\Stmt\If_(new \ECSPrefix202408\PhpParser\Node\Expr\Variable($paramName), ['stmts' => [new \ECSPrefix202408\PhpParser\Node\Stmt\Expression(new \ECSPrefix202408\PhpParser\Node\Expr\Assign(new \ECSPrefix202408\PhpParser\Node\Expr\ArrayDimFetch($dynamicSetsPropertyFetch), new \ECSPrefix202408\PhpParser\Node\Scalar\String_($setName)))]]);
}
function lowercaseUntilFirstLower($input)
{
    $output = '';
    $foundLower = \false;
    for ($i = 0; $i < \strlen($input); $i++) {
        $char = $input[$i];
        if (!$foundLower && \ctype_upper($char)) {
            $output .= \strtolower($char);
        } else {
            $output .= $char;
            $foundLower = \true;
        }
    }
    return $output;
}
// add dynamic set includes
$classMethod->stmts[] = new \ECSPrefix202408\PhpParser\Node\Stmt\Return_(new \ECSPrefix202408\PhpParser\Node\Expr\Variable('this'));
$printerStandard = new \ECSPrefix202408\PhpParser\PrettyPrinter\Standard();
echo $printerStandard->prettyPrint([$classMethod]);
