<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Application;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
final class SniffMetadataCollector
{
    /**
     * @var array<class-string|string>
     */
    private $appliedSniffs = [];
    /**
     * @var CodingStandardError[]
     */
    private $codingStandardErrors = [];
    /**
     * @param class-string<Sniff>|string $checkerClass
     * @return void
     */
    public function addAppliedSniff(string $checkerClass)
    {
        $this->appliedSniffs[] = $checkerClass;
    }
    /**
     * @return array<class-string<Sniff>|string>
     */
    public function getAppliedSniffs() : array
    {
        return $this->appliedSniffs;
    }
    /**
     * @return void
     */
    public function reset()
    {
        $this->appliedSniffs = [];
        $this->codingStandardErrors = [];
    }
    /**
     * @return void
     */
    public function addCodingStandardError(\Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError $codingStandardError)
    {
        $this->codingStandardErrors[] = $codingStandardError;
    }
    /**
     * @return CodingStandardError[]
     */
    public function getCodingStandardErrors() : array
    {
        return $this->codingStandardErrors;
    }
}
