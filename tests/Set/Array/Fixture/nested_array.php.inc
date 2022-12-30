<?php

declare(strict_types=1);

$test = ['key' => ['keyA' => 'valueA']];

?>
-----
<?php

declare(strict_types=1);

$test = [
    'key' => [
        'keyA' => 'valueA',
    ],
];

?>
