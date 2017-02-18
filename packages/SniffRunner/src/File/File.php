<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\File;

use PHP_CodeSniffer\Files\File as BaseFile;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;
use Symplify\EasyCodingStandard\SniffRunner\Contract\File\FileInterface;
use Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException;
use Symplify\EasyCodingStandard\Report\ErrorDataCollector;

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
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    /**
     * @var bool
     */
    private $isFixer;

    public function __construct(
        string $path,
        array $tokens,
        Fixer $fixer,
        ErrorDataCollector $errorDataCollector,
        bool $isFixer
    ) {
        $this->path = $path;
        $this->tokens = $tokens;
        $this->fixer = $fixer;
        $this->errorDataCollector = $errorDataCollector;

        $this->numTokens = count($this->tokens);
        $this->content = file_get_contents($path);
        $this->isFixer = $isFixer;

        $this->eolChar = PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function parse()
    {
        throw new NotImplementedException(sprintf(
            'Method %s not needed to be public. File is already parsed on __construct.',
            __METHOD__
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
        throw new NotImplementedException(sprintf(
            'Method "%s" is not needed to be public. Use external processing.',
            __METHOD__
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCount()
    {
        throw new NotImplementedException(sprintf(
            'Method "%s" is not needed to be public. Use "%s" service.',
            __METHOD__,
            ErrorDataCollector::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        throw new NotImplementedException(sprintf(
            'Method "%s" is not needed to be public. Use "%s" service.',
            __METHOD__,
            ErrorDataCollector::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function addFixableError($error, $stackPtr, $code, $data = [], $severity = 0) : bool
    {
        $this->addError($error, $stackPtr, $code, $data, $severity, true);
        return $this->isFixer;
    }

    /**
     * {@inheritdoc}
     */
    protected function addMessage($error, $message, $line, $column, $code, $data, $severity, $isFixable = false) : bool
    {
        if (!$error) { // skip warnings
            return false;
        }

        $this->errorDataCollector->addErrorMessage(
            $this->path, $message, $line, $code, $data, $isFixable
        );

        return true;
    }
}
