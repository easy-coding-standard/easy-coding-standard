<?php

namespace ECSPrefix20210516\Symplify\RuleDocGenerator\ValueObject\CodeSample;

use ECSPrefix20210516\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
final class ExtraFileCodeSample extends \ECSPrefix20210516\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample
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
        $badCode = (string) $badCode;
        $goodCode = (string) $goodCode;
        $extraFile = (string) $extraFile;
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
