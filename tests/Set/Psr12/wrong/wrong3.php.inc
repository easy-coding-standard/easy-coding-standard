<?php

class SomeClass
{
    public function lottaArguments($arg1,
                                   $arg2, $arg3) {
    }
}

function lottaArguments($arg1,& $arg2 ,
                        $arg3=[]) :?string
{
    // function body
}

for( $i=1 ;$i<2 ;$i++ ){
}
for($i=1;$i<2;$i++)
{
}

function ($a,
          $b, $c,
          $d){
};

$foo->bar(
    $arg1,
    function ($a,
              $b, $c,
              $d) use ($e){
        // Body
    },
    $arg3
);
