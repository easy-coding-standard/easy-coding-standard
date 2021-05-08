<?php

namespace Symplify\RuleDocGenerator\ValueObject\CodeSample;

use Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
final class ExtraFileCodeSample extends \Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample
{
    /**
     * @var string
     */
    private $extraFile;
    /**
     * @param string $badCode
     * @param string $goodCode
     * @param string $extraFile
     */
    public function __construct($badCode, $goodCode, $extraFile)
    {
        if (\is_object($extraFile)) {
            $extraFile = (string) $extraFile;
        }
        if (\is_object($goodCode)) {
            $goodCode = (string) $goodCode;
        }
        if (\is_object($badCode)) {
            $badCode = (string) $badCode;
        }
        parent::__construct($badCode, $goodCode);
        $this->extraFile = $extraFile;
    }
    /**
     * @return string
     */
    public function getExtraFile()
    {
        return $this->extraFile;
    }
}
