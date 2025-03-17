<?php

/* testSwitchNormalSyntax */
switch ($value) {
    case 1:
        echo 'one';
        break;
    default :
        echo 'other';
        break;
}

// Test for https://github.com/squizlabs/PHP_CodeSniffer/issues/497
/* testSwitchAlternativeSyntax */
switch ($value):
    /* testSwitchAlternativeSyntaxEnsureTestWillNotPickUpWrongColon */
    case 1:
        echo 'one';
        break;
    default:
        echo 'other';
        break;
/* testSwitchAlternativeSyntaxEnd */
endswitch;

// Test for https://github.com/squizlabs/PHP_CodeSniffer/issues/543
/* testSwitchClosureWithinCondition */
switch((function () {
    return 'bar';
})()) /* testSwitchClosureWithinConditionScopeOpener */ {
    case 1:
        return 'test';
}
