<?php

testFunction([ 'a' => 'xyz', 'b' => 'pqr' ]);

testFunction([]);

testFunction([ 'a' => 'xyz', 'b' => 'pqr' ]);

?>
-----
<?php

testFunction([
    'a' => 'xyz',
    'b' => 'pqr',
]);

testFunction([]);

testFunction([
    'a' => 'xyz',
    'b' => 'pqr',
]);

?>
