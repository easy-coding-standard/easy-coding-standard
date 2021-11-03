<?php

// fixes missing class on autoload

declare(strict_types=1);

require_once __DIR__ . '/vendor/psr/container/src/ContainerExceptionInterface.php';
require_once __DIR__ . '/vendor/psr/container/src/NotFoundExceptionInterface.php';
require_once __DIR__ . '/vendor/psr/container/src/ContainerInterface.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/ValueObject/RuleDefinition.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/Contract/DocumentedRuleInterface.php';
require_once __DIR__ . '/vendor/react/promise/src/functions.php';
