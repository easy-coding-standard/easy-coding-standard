<?php

declare(strict_types=1);

/** Control Structures */
if( $simple  ==  true )$doSomething=true;
if($expr1)
{
    // if body
}
else if($expr2){
    // elseif body
}
else
{
    // else body
}
if (
    $expr1
    && $expr2
)
{
    // if body
} elseif (
    $expr3
    && $expr4
) {
    // elseif body
}

switch( $simple ){
    case $test:
        break;
}
switch($simple)
{
    case $test:
        break;
}

while( $simple )
    $doSomething = true;
while($simple)
{
}

do
{
    $someBody = 'content';

}
while( $simple );
do{
}while($simple);

foreach( $simple as $key=>$value ){
}
foreach($simple as $key=>$value)
{
}

try
{
    // try body
}
catch(FirstThrowableType $e)
{
    // catch body
}
catch(OtherThrowableType $e)
{
    // catch body
}finally{
    // finally body
}

/** Operators */

// Arithmetic
$result=-$a+$b-( +$c*$d )/  $e%($f**$g);

// Comparison
((!$a==$b)   ===  ($c!=$d))<>( ($e<$f)<=>($g>$h));

// Assignment
$a=$b+=$c  -=  $d.=$e=&$f;

// Bitwise
$b&$c|~$d^$f<<$g>>$h;

// Logical
$a and$b   or   !$c xor$d&&$e||$f;

// String
$a.$b;

// Single pipe try/catch operator
try {
} catch (Exception|Error $a) {
}

/** Closures */
function( $a , $b )use($c){};

function   ()  use  (  $a  )  {  }  ;

function ($a, $b)
{};

function ($a, $b)
{
    // Body


};

$closureWithArgs = function ($arg1, $arg2)
{
    // body
};

$closureWithArgsAndVars = function( $arg1 ,$arg2 )
use( $var1, $var2 ){
    // body
};

/** Anonymous Classes */
new class(
    $a,$b,$c
) extends SomeExtendedClass implements
    \ArrayAccess,
    \Countable {
    // Body
};

?>
-----
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

switch ($simple) {
    case $test:
        break;
}
switch ($simple) {
    case $test:
        break;
}

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

$closureWithArgs = function ($arg1, $arg2) {
    // body
};

$closureWithArgsAndVars = function ($arg1, $arg2) use ($var1, $var2) {
    // body
};

/** Anonymous Classes */
new class($a, $b, $c) extends SomeExtendedClass implements
    \ArrayAccess,
    \Countable {
    // Body
};
