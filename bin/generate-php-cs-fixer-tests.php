<?php

declare(strict_types=1);

use PhpCsFixer\RuleSet\RuleSets;

// this helper script generates the withPreparedPhpCsFixerSets() method for ECSConfigBuilder class

require __DIR__ . '/../vendor/autoload.php';

$setsDirectory = __DIR__ . '/../vendor/friendsofphp/php-cs-fixer/src/RuleSet/Sets/';

$setDefinitions = RuleSets::getSetDefinitions();

$setNames = [];
foreach ($setDefinitions as $setDefinition) {
    $setNames[] = $setDefinition->getName();
}

// create withPreparedPhpCsFixerSets() method here
$classMethod = new \PhpParser\Node\Stmt\ClassMethod('withPreparedPhpCsFixerSets');
$classMethod->flags = \PhpParser\Node\Stmt\Class_::MODIFIER_PUBLIC;
$classMethod->returnType = new \PhpParser\Node\Name('self');

foreach ($setNames as $setName) {
    // convert to PHP variable name
    $paramName = ltrim( $setName, '@');

    $paramName = lowercaseUntilFirstLower($paramName);
    $paramName = str_replace(':r', 'R', $paramName);
    $paramName = str_replace(['.', '-', '_'], '', $paramName);

    // lowercase only the first uppercase letters

    $classMethod->params[] = new \PhpParser\Node\Param(
        new \PhpParser\Node\Expr\Variable($paramName),
        new \PhpParser\Node\Expr\ConstFetch(new \PhpParser\Node\Name('false')),
        new \PhpParser\Node\Identifier('bool')
    );

    $dynamicSetsPropertyFetch = new \PhpParser\Node\Expr\PropertyFetch(new \PhpParser\Node\Expr\Variable('this'), 'dynamicSets');

    $classMethod->stmts[] = new \PhpParser\Node\Stmt\If_(new \PhpParser\Node\Expr\Variable($paramName), [
        'stmts' => [
            new \PhpParser\Node\Stmt\Expression(new \PhpParser\Node\Expr\Assign(
                new \PhpParser\Node\Expr\ArrayDimFetch($dynamicSetsPropertyFetch),
                new \PhpParser\Node\Scalar\String_($setName)
            ))
        ]
    ]);
}


function lowercaseUntilFirstLower($input) {
    $output = '';
    $foundLower = false;

    for ($i = 0; $i < strlen($input); $i++) {
        $char = $input[$i];

        if (!$foundLower && ctype_upper($char)) {
            $output .= strtolower($char);
        } else {
            $output .= $char;
            $foundLower = true;
        }
    }

    return $output;
}
// add dynamic set includes

$classMethod->stmts[] = new \PhpParser\Node\Stmt\Return_(new \PhpParser\Node\Expr\Variable('this'));


$printerStandard = new \PhpParser\PrettyPrinter\Standard();
echo $printerStandard->prettyPrint([$classMethod]);
