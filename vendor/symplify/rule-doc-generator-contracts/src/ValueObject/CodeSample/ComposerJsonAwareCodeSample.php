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
     */
    public function __construct($badCode, string $goodCode, string $composerJson)
    {
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
