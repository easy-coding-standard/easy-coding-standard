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
 * Reflection class for a {@}deprecated tag in a Docblock.
 */
final class Deprecated extends \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\BaseTag implements \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod
{
    /** @var string */
    protected $name = 'deprecated';
    /**
     * PCRE regular expression matching a version vector.
     * Assumes the "x" modifier.
     */
    public const REGEX_VECTOR = '(?:
        # Normal release vectors.
        \\d\\S*
        |
        # VCS version vectors. Per PHPCS, they are expected to
        # follow the form of the VCS name, followed by ":", followed
        # by the version vector itself.
        # By convention, popular VCSes like CVS, SVN and GIT use "$"
        # around the actual version vector.
        [^\\s\\:]+\\:\\s*\\$[^\\$]+\\$
    )';
    /** @var string|null The version vector. */
    private $version;
    public function __construct(?string $version = null, ?\ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Description $description = null)
    {
        \ECSPrefix20210804\Webmozart\Assert\Assert::nullOrNotEmpty($version);
        $this->version = $version;
        $this->description = $description;
    }
    /**
     * @return static
     */
    public static function create(?string $body, ?\ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\DescriptionFactory $descriptionFactory = null, ?\ECSPrefix20210804\phpDocumentor\Reflection\Types\Context $context = null) : self
    {
        if (empty($body)) {
            return new static();
        }
        $matches = [];
        if (!\preg_match('/^(' . self::REGEX_VECTOR . ')\\s*(.+)?$/sux', $body, $matches)) {
            return new static(null, $descriptionFactory !== null ? $descriptionFactory->create($body, $context) : null);
        }
        \ECSPrefix20210804\Webmozart\Assert\Assert::notNull($descriptionFactory);
        return new static($matches[1], $descriptionFactory->create($matches[2] ?? '', $context));
    }
    /**
     * Gets the version section of the tag.
     */
    public function getVersion() : ?string
    {
        return $this->version;
    }
    /**
     * Returns a string representation for this tag.
     */
    public function __toString() : string
    {
        if ($this->description) {
            $description = $this->description->render();
        } else {
            $description = '';
        }
        $version = (string) $this->version;
        return $version . ($description !== '' ? ($version !== '' ? ' ' : '') . $description : '');
    }
}
