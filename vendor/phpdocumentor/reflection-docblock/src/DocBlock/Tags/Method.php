<?php

declare (strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */
namespace ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags;

use InvalidArgumentException;
use ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Description;
use ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use ECSPrefix20210804\phpDocumentor\Reflection\Type;
use ECSPrefix20210804\phpDocumentor\Reflection\TypeResolver;
use ECSPrefix20210804\phpDocumentor\Reflection\Types\Context as TypeContext;
use ECSPrefix20210804\phpDocumentor\Reflection\Types\Mixed_;
use ECSPrefix20210804\phpDocumentor\Reflection\Types\Void_;
use ECSPrefix20210804\Webmozart\Assert\Assert;
use function array_keys;
use function explode;
use function implode;
use function is_string;
use function preg_match;
use function sort;
use function strpos;
use function substr;
use function trim;
use function var_export;
/**
 * Reflection class for an {@}method in a Docblock.
 */
final class Method extends \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\BaseTag implements \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod
{
    /** @var string */
    protected $name = 'method';
    /** @var string */
    private $methodName;
    /**
     * @phpstan-var array<int, array{name: string, type: Type}>
     * @var array<int, array<string, Type|string>>
     */
    private $arguments;
    /** @var bool */
    private $isStatic;
    /** @var Type */
    private $returnType;
    /**
     * @param array<int, array<string, Type|string>> $arguments
     *
     * @phpstan-param array<int, array{name: string, type: Type}|string> $arguments
     */
    public function __construct(string $methodName, array $arguments = [], ?\ECSPrefix20210804\phpDocumentor\Reflection\Type $returnType = null, bool $static = \false, ?\ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Description $description = null)
    {
        \ECSPrefix20210804\Webmozart\Assert\Assert::stringNotEmpty($methodName);
        if ($returnType === null) {
            $returnType = new \ECSPrefix20210804\phpDocumentor\Reflection\Types\Void_();
        }
        $this->methodName = $methodName;
        $this->arguments = $this->filterArguments($arguments);
        $this->returnType = $returnType;
        $this->isStatic = $static;
        $this->description = $description;
    }
    public static function create(string $body, ?\ECSPrefix20210804\phpDocumentor\Reflection\TypeResolver $typeResolver = null, ?\ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\DescriptionFactory $descriptionFactory = null, ?\ECSPrefix20210804\phpDocumentor\Reflection\Types\Context $context = null) : ?self
    {
        \ECSPrefix20210804\Webmozart\Assert\Assert::stringNotEmpty($body);
        \ECSPrefix20210804\Webmozart\Assert\Assert::notNull($typeResolver);
        \ECSPrefix20210804\Webmozart\Assert\Assert::notNull($descriptionFactory);
        // 1. none or more whitespace
        // 2. optionally the keyword "static" followed by whitespace
        // 3. optionally a word with underscores followed by whitespace : as
        //    type for the return value
        // 4. then optionally a word with underscores followed by () and
        //    whitespace : as method name as used by phpDocumentor
        // 5. then a word with underscores, followed by ( and any character
        //    until a ) and whitespace : as method name with signature
        // 6. any remaining text : as description
        if (!\preg_match('/^
                # Static keyword
                # Declares a static method ONLY if type is also present
                (?:
                    (static)
                    \\s+
                )?
                # Return type
                (?:
                    (
                        (?:[\\w\\|_\\\\]*\\$this[\\w\\|_\\\\]*)
                        |
                        (?:
                            (?:[\\w\\|_\\\\]+)
                            # array notation
                            (?:\\[\\])*
                        )*+
                    )
                    \\s+
                )?
                # Method name
                ([\\w_]+)
                # Arguments
                (?:
                    \\(([^\\)]*)\\)
                )?
                \\s*
                # Description
                (.*)
            $/sux', $body, $matches)) {
            return null;
        }
        [, $static, $returnType, $methodName, $argumentLines, $description] = $matches;
        $static = $static === 'static';
        if ($returnType === '') {
            $returnType = 'void';
        }
        $returnType = $typeResolver->resolve($returnType, $context);
        $description = $descriptionFactory->create($description, $context);
        /** @phpstan-var array<int, array{name: string, type: Type}> $arguments */
        $arguments = [];
        if ($argumentLines !== '') {
            $argumentsExploded = \explode(',', $argumentLines);
            foreach ($argumentsExploded as $argument) {
                $argument = \explode(' ', self::stripRestArg(\trim($argument)), 2);
                if (\strpos($argument[0], '$') === 0) {
                    $argumentName = \substr($argument[0], 1);
                    $argumentType = new \ECSPrefix20210804\phpDocumentor\Reflection\Types\Mixed_();
                } else {
                    $argumentType = $typeResolver->resolve($argument[0], $context);
                    $argumentName = '';
                    if (isset($argument[1])) {
                        $argument[1] = self::stripRestArg($argument[1]);
                        $argumentName = \substr($argument[1], 1);
                    }
                }
                $arguments[] = ['name' => $argumentName, 'type' => $argumentType];
            }
        }
        return new static($methodName, $arguments, $returnType, $static, $description);
    }
    /**
     * Retrieves the method name.
     */
    public function getMethodName() : string
    {
        return $this->methodName;
    }
    /**
     * @return array<int, array<string, Type|string>>
     *
     * @phpstan-return array<int, array{name: string, type: Type}>
     */
    public function getArguments() : array
    {
        return $this->arguments;
    }
    /**
     * Checks whether the method tag describes a static method or not.
     *
     * @return bool TRUE if the method declaration is for a static method, FALSE otherwise.
     */
    public function isStatic() : bool
    {
        return $this->isStatic;
    }
    public function getReturnType() : \ECSPrefix20210804\phpDocumentor\Reflection\Type
    {
        return $this->returnType;
    }
    public function __toString() : string
    {
        $arguments = [];
        foreach ($this->arguments as $argument) {
            $arguments[] = $argument['type'] . ' $' . $argument['name'];
        }
        $argumentStr = '(' . \implode(', ', $arguments) . ')';
        if ($this->description) {
            $description = $this->description->render();
        } else {
            $description = '';
        }
        $static = $this->isStatic ? 'static' : '';
        $returnType = (string) $this->returnType;
        $methodName = (string) $this->methodName;
        return $static . ($returnType !== '' ? ($static !== '' ? ' ' : '') . $returnType : '') . ($methodName !== '' ? ($static !== '' || $returnType !== '' ? ' ' : '') . $methodName : '') . $argumentStr . ($description !== '' ? ' ' . $description : '');
    }
    /**
     * @param mixed[][]|string[] $arguments
     *
     * @return mixed[][]
     *
     * @phpstan-param array<int, array{name: string, type: Type}|string> $arguments
     * @phpstan-return array<int, array{name: string, type: Type}>
     */
    private function filterArguments(array $arguments = []) : array
    {
        $result = [];
        foreach ($arguments as $argument) {
            if (\is_string($argument)) {
                $argument = ['name' => $argument];
            }
            if (!isset($argument['type'])) {
                $argument['type'] = new \ECSPrefix20210804\phpDocumentor\Reflection\Types\Mixed_();
            }
            $keys = \array_keys($argument);
            \sort($keys);
            if ($keys !== ['name', 'type']) {
                throw new \InvalidArgumentException('Arguments can only have the "name" and "type" fields, found: ' . \var_export($keys, \true));
            }
            $result[] = $argument;
        }
        return $result;
    }
    private static function stripRestArg(string $argument) : string
    {
        if (\strpos($argument, '...') === 0) {
            $argument = \trim(\substr($argument, 3));
        }
        return $argument;
    }
}
