<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Fixer;

use Symplify\EasyCodingStandard\SniffRunner\File\File;
use function Safe\substr;

final class Fixer
{
    /**
     * For back compatibility with PHP_CodeSniffer.
     *
     * @var int
     */
    public $loops;

    /**
     * For back compatibility with PHP_CodeSniffer.
     *
     * @var bool
     */
    public $enabled = true;

    /**
     * @var int[]|string[]
     */
    private $tokens = [];

    /**
     * Is there an open changeset.
     *
     * @var bool
     */
    private $inChangeset = false;

    /**
     * @var string[]
     */
    private $changeset = [];

    /**
     * @var string[]
     */
    private $fixedTokens = [];

    public function startFile(File $file): void
    {
        $tokens = $file->getTokens();

        $this->fixedTokens = [];
        $this->tokens = [];
        foreach ($tokens as $index => $token) {
            $this->tokens[$index] = $token['orig_content'] ?? $token['content'];
        }
    }

    public function getContents(): string
    {
        return implode($this->tokens);
    }

    public function getTokenContent(int $stackPtr): string
    {
        if ($this->inChangeset === true && isset($this->changeset[$stackPtr])) {
            return $this->changeset[$stackPtr];
        }

        return (string) $this->tokens[$stackPtr];
    }

    public function replaceToken(int $stackPtr, string $content): bool
    {
        if ($this->inChangeset === true) {
            $this->changeset[$stackPtr] = $content;

            return true;
        }

        if (isset($this->fixedTokens[$stackPtr])) {
            return false;
        }

        $this->fixedTokens[$stackPtr] = (string) $this->tokens[$stackPtr] ?? $content;
        $this->tokens[$stackPtr] = $content;

        return true;
    }

    /**
     * Name is for back compatibility. Better would be "addContentAfter".
     */
    public function addContent(int $stackPtr, string $content): bool
    {
        $current = $this->getTokenContent($stackPtr);

        return $this->replaceToken($stackPtr, $current . $content);
    }

    public function addContentBefore(int $stackPtr, string $content): bool
    {
        $current = $this->getTokenContent($stackPtr);

        return $this->replaceToken($stackPtr, $content . $current);
    }

    public function addNewline(int $stackPtr): bool
    {
        return $this->addContent($stackPtr, PHP_EOL);
    }

    public function addNewlineBefore(int $stackPtr): bool
    {
        return $this->addContentBefore($stackPtr, PHP_EOL);
    }

    public function substrToken(int $stackPtr, int $start, ?int $length = null): bool
    {
        $current = $this->getTokenContent($stackPtr);

        if ($length !== null) {
            $newContent = substr($current, $start, $length);
        } else {
            $newContent = substr($current, $start);
        }

        return $this->replaceToken($stackPtr, $newContent);
    }

    /**
     * Start recording actions for a changeset.
     */
    public function beginChangeSet(): void
    {
        $this->changeset = [];
        $this->inChangeset = true;
    }

    /**
     * Stop recording actions for a changeset, and apply logged changes.
     */
    public function endChangeSet(): void
    {
        $this->inChangeset = false;

        foreach ($this->changeset as $stackPtr => $content) {
            $this->replaceToken($stackPtr, $content);
        }

        $this->changeset = [];
    }
}
