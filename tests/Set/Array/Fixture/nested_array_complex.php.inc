<?php

declare(strict_types=1);

$test = ['keyA' => ['keyA1' => 'valueA1'], 'keyB' => ['keyB1' => 'valueB1']];

?>
-----
<?php

declare(strict_types=1);

$test = [
    'keyA' => [
        'keyA1' => 'valueA1',
    ],
    'keyB' => [
        'keyB1' => 'valueB1',
    ],
];

?>
