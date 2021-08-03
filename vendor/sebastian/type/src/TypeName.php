<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\SebastianBergmann\Type;

use function array_pop;
use function explode;
use function implode;
use function substr;
use ReflectionClass;
final class TypeName
{
    /**
     * @var ?string
     */
    private $namespaceName;
    /**
     * @var string
     */
    private $simpleName;
    /**
     * @return $this
     */
    public static function fromQualifiedName(string $fullClassName)
    {
        if ($fullClassName[0] === '\\') {
            $fullClassName = \substr($fullClassName, 1);
        }
        $classNameParts = \explode('\\', $fullClassName);
        $simpleName = \array_pop($classNameParts);
        $namespaceName = \implode('\\', $classNameParts);
        return new self($namespaceName, $simpleName);
    }
    /**
     * @return $this
     */
    public static function fromReflection(\ReflectionClass $type)
    {
        return new self($type->getNamespaceName(), $type->getShortName());
    }
    /**
     * @param string|null $namespaceName
     */
    public function __construct($namespaceName, string $simpleName)
    {
        if ($namespaceName === '') {
            $namespaceName = null;
        }
        $this->namespaceName = $namespaceName;
        $this->simpleName = $simpleName;
    }
    /**
     * @return string|null
     */
    public function namespaceName()
    {
        return $this->namespaceName;
    }
    public function simpleName() : string
    {
        return $this->simpleName;
    }
    public function qualifiedName() : string
    {
        return $this->namespaceName === null ? $this->simpleName : $this->namespaceName . '\\' . $this->simpleName;
    }
    /**
     * @deprecated Use namespaceName() instead
     *
     * @codeCoverageIgnore
     * @return string|null
     */
    public function getNamespaceName()
    {
        return $this->namespaceName();
    }
    /**
     * @deprecated Use simpleName() instead
     *
     * @codeCoverageIgnore
     */
    public function getSimpleName() : string
    {
        return $this->simpleName();
    }
    /**
     * @deprecated Use qualifiedName() instead
     *
     * @codeCoverageIgnore
     */
    public function getQualifiedName() : string
    {
        return $this->qualifiedName();
    }
    public function isNamespaced() : bool
    {
        return $this->namespaceName !== null;
    }
}
