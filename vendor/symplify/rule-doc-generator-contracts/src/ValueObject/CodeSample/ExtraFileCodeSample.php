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
     */
    public function __construct($badCode, string $goodCode, string $extraFile)
    {
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
