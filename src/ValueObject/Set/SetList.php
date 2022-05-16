<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\ValueObject\Set;

final class SetList
{
    /**
     * @var string
     * @api
     */
    public const PSR_12 = __DIR__ . '/../../../config/set/psr12.php';
    /**
     * @deprecated This set is outdated and will be removed in the future. Use PSR-12 or directly https://github.com/FriendsOfPHP/PHP-CS-Fixer/tree/master/src/RuleSet/Sets
     * @var string
     * @api
     */
    public const PHP_CS_FIXER = __DIR__ . '/../../../config/set/php-cs-fixer.php';
    /**
     * @deprecated This set is outdated and will be removed in the future. Use PSR-12 or directly https://github.com/FriendsOfPHP/PHP-CS-Fixer/tree/master/src/RuleSet/Sets
     * @var string
     * @api
     */
    public const PHP_CS_FIXER_RISKY = __DIR__ . '/../../../config/set/php-cs-fixer-risky.php';
    /**
     * @var string
     * @api
     */
    public const CLEAN_CODE = __DIR__ . '/../../../config/set/clean-code.php';
    /**
     * @var string
     * @api
     */
    public const SYMPLIFY = __DIR__ . '/../../../config/set/symplify.php';
    /**
     * @var string
     * @api
     */
    public const ARRAY = __DIR__ . '/../../../config/set/common/array.php';
    /**
     * @var string
     * @api
     */
    public const COMMON = __DIR__ . '/../../../config/set/common.php';
    /**
     * @var string
     * @api
     */
    public const COMMENTS = __DIR__ . '/../../../config/set/common/comments.php';
    /**
     * @var string
     * @api
     */
    public const CONTROL_STRUCTURES = __DIR__ . '/../../../config/set/common/control-structures.php';
    /**
     * @var string
     * @api
     */
    public const DOCBLOCK = __DIR__ . '/../../../config/set/common/docblock.php';
    /**
     * @var string
     * @api
     */
    public const NAMESPACES = __DIR__ . '/../../../config/set/common/namespaces.php';
    /**
     * @var string
     * @api
     */
    public const PHPUNIT = __DIR__ . '/../../../config/set/common/phpunit.php';
    /**
     * @var string
     * @api
     */
    public const SPACES = __DIR__ . '/../../../config/set/common/spaces.php';
    /**
     * @var string
     * @api
     */
    public const STRICT = __DIR__ . '/../../../config/set/common/strict.php';
    /**
     * @deprecated This set is outdated and will be removed in the future. Use PSR-12 or directly https://github.com/FriendsOfPHP/PHP-CS-Fixer/tree/master/src/RuleSet/Sets
     * @var string
     * @api
     */
    public const SYMFONY = __DIR__ . '/../../../config/set/symfony.php';
    /**
     * @deprecated This set is outdated and will be removed in the future. Use PSR-12 or directly https://github.com/FriendsOfPHP/PHP-CS-Fixer/tree/master/src/RuleSet/Sets
     * @var string
     * @api
     */
    public const SYMFONY_RISKY = __DIR__ . '/../../../config/set/symfony-risky.php';
    /**
     * @var string
     * @api
     */
    public const DOCTRINE_ANNOTATIONS = __DIR__ . '/../../../config/set/doctrine-annotations.php';
}
