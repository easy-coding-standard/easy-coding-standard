<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use PHP_CodeSniffer\Files\File as BaseFile;
use Symplify\EasyCodingStandard\Report\ErrorCollector;
use Symplify\EasyCodingStandard\SniffRunner\Contract\File\FileInterface;
use Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;

final class File extends BaseFile implements FileInterface
{
    /**
     * @var string
     */
    public $tokenizerType = 'PHP';

    /**
     * @var Fixer
     */
    public $fixer;

    /**
     * @var ErrorCollector
     */
    private $errorDataCollector;

    /**
     * @var bool
     */
    private $isFixer;

    /**
     * @param string $path
     * @param array[] $tokens
     * @param Fixer $fixer
     * @param ErrorCollector $errorCollector
     * @param bool $isFixer
     */
    public function __construct(
        string $path,
        array $tokens,
        Fixer $fixer,
        ErrorCollector $errorCollector,
        bool $isFixer
    ) {
        $this->path = $path;
        $this->tokens = $tokens;
        $this->fixer = $fixer;
        $this->errorDataCollector = $errorCollector;

        $this->numTokens = count($this->tokens);
        $this->content = file_get_contents($path);
        $this->isFixer = $isFixer;

        $this->eolChar = PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(): void
    {
        throw new NotImplementedException(sprintf(
            'Method %s not needed to be public. File is already parsed on __construct.',
            __METHOD__
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function process(): void
    {
        throw new NotImplementedException(sprintf(
            'Method "%s" is not needed to be public. Use external processing.',
            __METHOD__
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCount(): void
    {
        throw new NotImplementedException(sprintf(
            'Method "%s" is not needed to be public. Use "%s" service.',
            __METHOD__,
            ErrorCollector::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors(): void
    {
        throw new NotImplementedException(sprintf(
            'Method "%s" is not needed to be public. Use "%s" service.',
            __METHOD__,
            ErrorCollector::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function addFixableError($error, $stackPtr, $code, $data = [], $severity = 0): bool
    {
        $this->addError($error, $stackPtr, $code, $data, $severity, true);
        return $this->isFixer;
    }

    /**
     * {@inheritdoc}
     */
    protected function addMessage($isError, $message, $line, $column, $code, $data, $severity, $isFixable = false): bool
    {
        if (! $isError) { // skip warnings
            return false;
        }

        if (count($data)) {
            $message = vsprintf($message, $data);
        }

        $this->errorDataCollector->addErrorMessage(
            $this->path, $message, $line, $code, $isFixable
        );

        return true;
    }
}
