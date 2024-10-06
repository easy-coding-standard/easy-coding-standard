<?php

declare(strict_types=1);

use PhpParser\Modifiers;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;
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
$classMethod = new ClassMethod('withPhpCsFixerSets');
$classMethod->flags = Modifiers::PUBLIC;
$classMethod->returnType = new Name('self');

foreach ($setNames as $setName) {
    // convert to PHP variable name
    $paramName = ltrim( $setName, '@');

    $paramName = lowercaseUntilFirstLower($paramName);
    $paramName = str_replace(':r', 'R', $paramName);
    $paramName = str_replace(['.', '-', '_'], '', $paramName);

    // lowercase only the first uppercase letters

    $classMethod->params[] = new Param(
        new Variable($paramName),
        new ConstFetch(new Name('false')),
        new Identifier('bool')
    );

    $dynamicSetsPropertyFetch = new PropertyFetch(new Variable('this'), 'dynamicSets');

    $classMethod->stmts[] = new If_(new Variable($paramName), [
        'stmts' => [
            new Expression(new Assign(
                new ArrayDimFetch($dynamicSetsPropertyFetch),
                new String_($setName)
            ))
        ]
    ]);
}


function lowercaseUntilFirstLower($input): string {
    $output = '';
    $foundLower = false;

    for ($i = 0; $i < strlen((string) $input); $i++) {
        $char = $input[$i];

        if (!$foundLower && ctype_upper((string) $char)) {
            $output .= strtolower((string) $char);
        } else {
            $output .= $char;
            $foundLower = true;
        }
    }

    return $output;
}

// add dynamic set includes

$classMethod->stmts[] = new Return_(new Variable('this'));


$printerStandard = new Standard();
echo $printerStandard->prettyPrint([$classMethod]);
