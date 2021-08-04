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
namespace ECSPrefix20210804\phpDocumentor\Reflection;

use InvalidArgumentException;
use LogicException;
use ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\StandardTagFactory;
use ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\Tag;
use ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\TagFactory;
use ECSPrefix20210804\Webmozart\Assert\Assert;
use function array_shift;
use function count;
use function explode;
use function is_object;
use function method_exists;
use function preg_match;
use function preg_replace;
use function str_replace;
use function strpos;
use function substr;
use function trim;
final class DocBlockFactory implements \ECSPrefix20210804\phpDocumentor\Reflection\DocBlockFactoryInterface
{
    /** @var DocBlock\DescriptionFactory */
    private $descriptionFactory;
    /** @var DocBlock\TagFactory */
    private $tagFactory;
    /**
     * Initializes this factory with the required subcontractors.
     */
    public function __construct(\ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\DescriptionFactory $descriptionFactory, \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\TagFactory $tagFactory)
    {
        $this->descriptionFactory = $descriptionFactory;
        $this->tagFactory = $tagFactory;
    }
    /**
     * Factory method for easy instantiation.
     *
     * @param array<string, class-string<Tag>> $additionalTags
     */
    public static function createInstance(array $additionalTags = []) : self
    {
        $fqsenResolver = new \ECSPrefix20210804\phpDocumentor\Reflection\FqsenResolver();
        $tagFactory = new \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\StandardTagFactory($fqsenResolver);
        $descriptionFactory = new \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock\DescriptionFactory($tagFactory);
        $tagFactory->addService($descriptionFactory);
        $tagFactory->addService(new \ECSPrefix20210804\phpDocumentor\Reflection\TypeResolver($fqsenResolver));
        $docBlockFactory = new self($descriptionFactory, $tagFactory);
        foreach ($additionalTags as $tagName => $tagHandler) {
            $docBlockFactory->registerTagHandler($tagName, $tagHandler);
        }
        return $docBlockFactory;
    }
    /**
     * @param object|string $docblock A string containing the DocBlock to parse or an object supporting the
     *                                getDocComment method (such as a ReflectionClass object).
     */
    public function create($docblock, ?\ECSPrefix20210804\phpDocumentor\Reflection\Types\Context $context = null, ?\ECSPrefix20210804\phpDocumentor\Reflection\Location $location = null) : \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock
    {
        if (\is_object($docblock)) {
            if (!\method_exists($docblock, 'getDocComment')) {
                $exceptionMessage = 'Invalid object passed; the given object must support the getDocComment method';
                throw new \InvalidArgumentException($exceptionMessage);
            }
            $docblock = $docblock->getDocComment();
            \ECSPrefix20210804\Webmozart\Assert\Assert::string($docblock);
        }
        \ECSPrefix20210804\Webmozart\Assert\Assert::stringNotEmpty($docblock);
        if ($context === null) {
            $context = new \ECSPrefix20210804\phpDocumentor\Reflection\Types\Context('');
        }
        $parts = $this->splitDocBlock($this->stripDocComment($docblock));
        [$templateMarker, $summary, $description, $tags] = $parts;
        return new \ECSPrefix20210804\phpDocumentor\Reflection\DocBlock($summary, $description ? $this->descriptionFactory->create($description, $context) : null, $this->parseTagBlock($tags, $context), $context, $location, $templateMarker === '#@+', $templateMarker === '#@-');
    }
    /**
     * @param class-string<Tag> $handler
     */
    public function registerTagHandler(string $tagName, string $handler) : void
    {
        $this->tagFactory->registerTagHandler($tagName, $handler);
    }
    /**
     * Strips the asterisks from the DocBlock comment.
     *
     * @param string $comment String containing the comment text.
     */
    private function stripDocComment(string $comment) : string
    {
        $comment = \preg_replace('#[ \\t]*(?:\\/\\*\\*|\\*\\/|\\*)?[ \\t]?(.*)?#u', '$1', $comment);
        \ECSPrefix20210804\Webmozart\Assert\Assert::string($comment);
        $comment = \trim($comment);
        // reg ex above is not able to remove */ from a single line docblock
        if (\substr($comment, -2) === '*/') {
            $comment = \trim(\substr($comment, 0, -2));
        }
        return \str_replace(["\r\n", "\r"], "\n", $comment);
    }
    // phpcs:disable
    /**
     * Splits the DocBlock into a template marker, summary, description and block of tags.
     *
     * @param string $comment Comment to split into the sub-parts.
     *
     * @return string[] containing the template marker (if any), summary, description and a string containing the tags.
     *
     * @author Mike van Riel <me@mikevanriel.com> for extending the regex with template marker support.
     *
     * @author Richard van Velzen (@_richardJ) Special thanks to Richard for the regex responsible for the split.
     */
    private function splitDocBlock(string $comment) : array
    {
        // phpcs:enable
        // Performance improvement cheat: if the first character is an @ then only tags are in this DocBlock. This
        // method does not split tags so we return this verbatim as the fourth result (tags). This saves us the
        // performance impact of running a regular expression
        if (\strpos($comment, '@') === 0) {
            return ['', '', '', $comment];
        }
        // clears all extra horizontal whitespace from the line endings to prevent parsing issues
        $comment = \preg_replace('/\\h*$/Sum', '', $comment);
        \ECSPrefix20210804\Webmozart\Assert\Assert::string($comment);
        /*
         * Splits the docblock into a template marker, summary, description and tags section.
         *
         * - The template marker is empty, #@+ or #@- if the DocBlock starts with either of those (a newline may
         *   occur after it and will be stripped).
         * - The short description is started from the first character until a dot is encountered followed by a
         *   newline OR two consecutive newlines (horizontal whitespace is taken into account to consider spacing
         *   errors). This is optional.
         * - The long description, any character until a new line is encountered followed by an @ and word
         *   characters (a tag). This is optional.
         * - Tags; the remaining characters
         *
         * Big thanks to RichardJ for contributing this Regular Expression
         */
        \preg_match('/
            \\A
            # 1. Extract the template marker
            (?:(\\#\\@\\+|\\#\\@\\-)\\n?)?

            # 2. Extract the summary
            (?:
              (?! @\\pL ) # The summary may not start with an @
              (
                [^\\n.]+
                (?:
                  (?! \\. \\n | \\n{2} )     # End summary upon a dot followed by newline or two newlines
                  [\\n.]* (?! [ \\t]* @\\pL ) # End summary when an @ is found as first character on a new line
                  [^\\n.]+                 # Include anything else
                )*
                \\.?
              )?
            )

            # 3. Extract the description
            (?:
              \\s*        # Some form of whitespace _must_ precede a description because a summary must be there
              (?! @\\pL ) # The description may not start with an @
              (
                [^\\n]+
                (?: \\n+
                  (?! [ \\t]* @\\pL ) # End description when an @ is found as first character on a new line
                  [^\\n]+            # Include anything else
                )*
              )
            )?

            # 4. Extract the tags (anything that follows)
            (\\s+ [\\s\\S]*)? # everything that follows
            /ux', $comment, $matches);
        \array_shift($matches);
        while (\count($matches) < 4) {
            $matches[] = '';
        }
        return $matches;
    }
    /**
     * Creates the tag objects.
     *
     * @param string        $tags    Tag block to parse.
     * @param Types\Context $context Context of the parsed Tag
     *
     * @return DocBlock\Tag[]
     */
    private function parseTagBlock(string $tags, \ECSPrefix20210804\phpDocumentor\Reflection\Types\Context $context) : array
    {
        $tags = $this->filterTagBlock($tags);
        if ($tags === null) {
            return [];
        }
        $result = [];
        $lines = $this->splitTagBlockIntoTagLines($tags);
        foreach ($lines as $key => $tagLine) {
            $result[$key] = $this->tagFactory->create(\trim($tagLine), $context);
        }
        return $result;
    }
    /**
     * @return string[]
     */
    private function splitTagBlockIntoTagLines(string $tags) : array
    {
        $result = [];
        foreach (\explode("\n", $tags) as $tagLine) {
            if ($tagLine !== '' && \strpos($tagLine, '@') === 0) {
                $result[] = $tagLine;
            } else {
                $result[\count($result) - 1] .= "\n" . $tagLine;
            }
        }
        return $result;
    }
    private function filterTagBlock(string $tags) : ?string
    {
        $tags = \trim($tags);
        if (!$tags) {
            return null;
        }
        if ($tags[0] !== '@') {
            // @codeCoverageIgnoreStart
            // Can't simulate this; this only happens if there is an error with the parsing of the DocBlock that
            // we didn't foresee.
            throw new \LogicException('A tag block started with text instead of an at-sign(@): ' . $tags);
            // @codeCoverageIgnoreEnd
        }
        return $tags;
    }
}
