<?php

declare (strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */
namespace ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags;

use ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Description;
use ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use ECSPrefix20210804\phpDocumentor\Reflection\Types\Context as TypeContext;
use ECSPrefix20210804\Webmozart\Assert\Assert;
use function preg_match;
/**
 * Reflection class for a {@}source tag in a Docblock.
 */
final class Source extends \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\BaseTag implements \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod
{
    /** @var string */
    protected $name = 'source';
    /** @var int The starting line, relative to the structural element's location. */
    private $startingLine;
    /** @var int|null The number of lines, relative to the starting line. NULL means "to the end". */
    private $lineCount;
    /**
     * @param int|string      $startingLine should be a to int convertible value
     * @param int|string|null $lineCount    should be a to int convertible value
     */
    public function __construct($startingLine, $lineCount = null, ?\ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Description $description = null)
    {
        \ECSPrefix20210804\Webmozart\Assert\Assert::integerish($startingLine);
        \ECSPrefix20210804\Webmozart\Assert\Assert::nullOrIntegerish($lineCount);
        $this->startingLine = (int) $startingLine;
        $this->lineCount = $lineCount !== null ? (int) $lineCount : null;
        $this->description = $description;
    }
    public static function create(string $body, ?\ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\DescriptionFactory $descriptionFactory = null, ?\ECSPrefix20210804\phpDocumentor\Reflection\Types\Context $context = null) : self
    {
        \ECSPrefix20210804\Webmozart\Assert\Assert::stringNotEmpty($body);
        \ECSPrefix20210804\Webmozart\Assert\Assert::notNull($descriptionFactory);
        $startingLine = 1;
        $lineCount = null;
        $description = null;
        // Starting line / Number of lines / Description
        if (\preg_match('/^([1-9]\\d*)\\s*(?:((?1))\\s+)?(.*)$/sux', $body, $matches)) {
            $startingLine = (int) $matches[1];
            if (isset($matches[2]) && $matches[2] !== '') {
                $lineCount = (int) $matches[2];
            }
            $description = $matches[3];
        }
        return new static($startingLine, $lineCount, $descriptionFactory->create($description ?? '', $context));
    }
    /**
     * Gets the starting line.
     *
     * @return int The starting line, relative to the structural element's
     *     location.
     */
    public function getStartingLine() : int
    {
        return $this->startingLine;
    }
    /**
     * Returns the number of lines.
     *
     * @return int|null The number of lines, relative to the starting line. NULL
     *     means "to the end".
     */
    public function getLineCount() : ?int
    {
        return $this->lineCount;
    }
    public function __toString() : string
    {
        if ($this->description) {
            $description = $this->description->render();
        } else {
            $description = '';
        }
        $startingLine = (string) $this->startingLine;
        $lineCount = $this->lineCount !== null ? '' . $this->lineCount : '';
        return $startingLine . ($lineCount !== '' ? ($startingLine || $startingLine === '0' ? ' ' : '') . $lineCount : '') . ($description !== '' ? ($startingLine || $startingLine === '0' || $lineCount !== '' ? ' ' : '') . $description : '');
    }
}
