<?php

declare(strict_types = 1);

require_once __DIR__ . '/vendor/psr/container/src/ContainerExceptionInterface.php';
require_once __DIR__ . '/vendor/psr/container/src/NotFoundExceptionInterface.php';
require_once __DIR__ . '/vendor/psr/container/src/ContainerInterface.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/Contract/ConfigurableRuleInterface.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/Contract/DocumentedRuleInterface.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/Contract/RuleCodeSamplePrinterInterface.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/Contract/CodeSampleInterface.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/Contract/Category/CategoryInfererInterface.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/Exception/PoorDocumentationException.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/Exception/ShouldNotHappenException.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/ValueObject/CodeSample/ComposerJsonAwareCodeSample.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/ValueObject/CodeSample/ExtraFileCodeSample.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/ValueObject/CodeSample/CodeSample.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/ValueObject/CodeSample/ConfiguredCodeSample.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/ValueObject/AbstractCodeSample.php';
require_once __DIR__ . '/vendor/symplify/rule-doc-generator-contracts/src/ValueObject/RuleDefinition.php';
