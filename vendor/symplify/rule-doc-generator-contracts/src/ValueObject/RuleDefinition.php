<?php

namespace Symplify\RuleDocGenerator\ValueObject;

use ECSPrefix20210512\Nette\Utils\Strings;
use Symplify\RuleDocGenerator\Contract\CodeSampleInterface;
use Symplify\RuleDocGenerator\Exception\PoorDocumentationException;
use Symplify\RuleDocGenerator\Exception\ShouldNotHappenException;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
final class RuleDefinition
{
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $ruleClass;
    /**
     * @var string
     */
    private $ruleFilePath;
    /**
     * @var CodeSampleInterface[]
     */
    private $codeSamples = [];
    /**
     * @param CodeSampleInterface[] $codeSamples
     * @param string $description
     */
    public function __construct($description, array $codeSamples)
    {
        $description = (string) $description;
        $this->description = $description;
        if ($codeSamples === []) {
            throw new \Symplify\RuleDocGenerator\Exception\PoorDocumentationException('Provide at least one code sample, so people can practically see what the rule does');
        }
        $this->codeSamples = $codeSamples;
    }
    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * @return void
     * @param string $ruleClass
     */
    public function setRuleClass($ruleClass)
    {
        $ruleClass = (string) $ruleClass;
        $this->ruleClass = $ruleClass;
    }
    /**
     * @return string
     */
    public function getRuleClass()
    {
        if ($this->ruleClass === null) {
            throw new \Symplify\RuleDocGenerator\Exception\ShouldNotHappenException();
        }
        return $this->ruleClass;
    }
    /**
     * @return void
     * @param string $ruleFilePath
     */
    public function setRuleFilePath($ruleFilePath)
    {
        $ruleFilePath = (string) $ruleFilePath;
        // fir relative file path for GitHub
        $this->ruleFilePath = \ltrim($ruleFilePath, '/');
    }
    /**
     * @return string
     */
    public function getRuleFilePath()
    {
        if ($this->ruleFilePath === null) {
            throw new \Symplify\RuleDocGenerator\Exception\ShouldNotHappenException();
        }
        return $this->ruleFilePath;
    }
    /**
     * @return string
     */
    public function getRuleShortClass()
    {
        return (string) \ECSPrefix20210512\Nette\Utils\Strings::after($this->ruleClass, '\\', -1);
    }
    /**
     * @return mixed[]
     */
    public function getCodeSamples()
    {
        return $this->codeSamples;
    }
    /**
     * @return bool
     */
    public function isConfigurable()
    {
        foreach ($this->codeSamples as $codeSample) {
            if ($codeSample instanceof \Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample) {
                return \true;
            }
        }
        return \false;
    }
}
