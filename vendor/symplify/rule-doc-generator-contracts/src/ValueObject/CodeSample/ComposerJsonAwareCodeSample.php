<?php

namespace Symplify\RuleDocGenerator\ValueObject\CodeSample;

use Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
final class ComposerJsonAwareCodeSample extends \Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample
{
    /**
     * @var string
     */
    private $composerJson;
    /**
     * @param string $badCode
     * @param string $goodCode
     * @param string $composerJson
     */
    public function __construct($badCode, $goodCode, $composerJson)
    {
        if (\is_object($composerJson)) {
            $composerJson = (string) $composerJson;
        }
        if (\is_object($goodCode)) {
            $goodCode = (string) $goodCode;
        }
        if (\is_object($badCode)) {
            $badCode = (string) $badCode;
        }
        parent::__construct($badCode, $goodCode);
        $this->composerJson = $composerJson;
    }
    /**
     * @return string
     */
    public function getComposerJson()
    {
        return $this->composerJson;
    }
}
