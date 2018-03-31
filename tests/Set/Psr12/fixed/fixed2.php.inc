<?php

declare(strict_types=1);

/** Control Structures */
if ($simple == true) {
    $doSomething = true;
}
if ($expr1) {
    // if body
} elseif ($expr2) {
    // elseif body
} else {
    // else body
}
if (
    $expr1
    && $expr2
) {
    // if body
} elseif (
    $expr3
    && $expr4
) {
    // elseif body
}
/**
 * @todo Make multiline controls adjust properly
 * @see 5.1

if ($expr1 ||
$expr2
&& $expr3) {
}*/

switch ($simple) {
    case $test:
        break;
}
switch ($simple) {
    case $test:
        break;
}
/**
 * @todo Make this indent properly
 * @see 5.2

switch($simple)
{
case $test:
break;
}*/

while ($simple) {
    $doSomething = true;
}
while ($simple) {
}

do {
    $someBody = 'content';
} while ($simple);
do {
} while ($simple);

foreach ($simple as $key => $value) {
}
foreach ($simple as $key => $value) {
}

try {
    // try body
} catch (FirstThrowableType $e) {
    // catch body
} catch (OtherThrowableType $e) {
    // catch body
} finally {
    // finally body
}

/** Operators */

// Arithmetic
$result = -$a + $b - (+$c * $d) / $e % ($f ** $g);

// Comparison
((!$a == $b) === ($c != $d)) <> (($e < $f) <=> ($g > $h));

// Assignment
$a = $b += $c -= $d .= $e = &$f;

// Bitwise
$b & $c | ~$d ^ $f << $g >> $h;

// Logical
$a and $b or !$c xor $d && $e || $f;

// String
$a . $b;

// Type
/** @todo Make instanceof operator spacing normalize to one space
$a instanceof$b;
 */

// Trait
/** @todo Make trait operator spacing normalize to one space
class Foo
{
use A,B
{
B::foo   insteadof     A;
A::bar   as    foobar;
}
}*/

// Single pipe try/catch operator
try {
} catch (Exception | Error $a) {
}

/** Closures */
function ($a, $b) use ($c) {
};

function () use ($a) {
};

function ($a, $b) {
};

function ($a, $b) {
    // Body
};

/** @todo Error if arguments with default values are followed by arguments with no default
 * @see 7
function ($a=1, $b) {
};*/

$closureWithArgs = function ($arg1, $arg2) {
    // body
};

$closureWithArgsAndVars = function ($arg1, $arg2) use ($var1, $var2) {
    // body
};

/** @todo Use statements should have spacing after a comma
function () use ($a,$b) {
}*/

/** @todo Align multiline "use" statements
 * @see 7
function ($a,
$b, $c,
$d) use ($e,
$f, $g) {
};*/

/** @todo Fix multiline indentation
 * @see 7
function ($a
$b, $c) {
}*/

/** Anonymous Classes */
new class($a, $b, $c) extends SomeExtendedClass implements
    \ArrayAccess,
    \Countable {
    // Body
};

/** @todo Support multiline anonymous class parameters
new class(
$a, $b, $c
) {
}*/

/** @todo Support next line bracket when parameters/implements are multiline
new class implements
\ArrayAccess,
\Countable{
}*/
