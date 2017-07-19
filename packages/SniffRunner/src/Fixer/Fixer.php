<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Fixer;

use Symplify\EasyCodingStandard\SniffRunner\File\File;

final class Fixer
{
    /**
     * For back compatibility with PHP_CodeSniffer.
     *
     * @var int
     */
    public $loops;

    /**
     * @var array<int, string>|string[]
     */
    private $tokens = [];

    public function startFile(File $file): void
    {
        $tokens = $file->getTokens();

        $this->tokens = [];
        foreach ($tokens as $index => $token) {
            if (isset($token['orig_content']) === true) {
                $this->tokens[$index] = $token['orig_content'];
            } else {
                $this->tokens[$index] = $token['content'];
            }
        }
    }

    public function getContents(): string
    {
        return implode($this->tokens);
    }

    public function getTokenContent(int $stackPtr): string
    {
        return $this->tokens[$stackPtr];
    }

    public function replaceToken(int $stackPtr, string $content): bool
    {
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
     * For BC.
     */
    public function beginChangeSet(): void
    {
    }

    /**
     * For BC.
     */
    public function endChangeSet(): void
    {
    }
}
