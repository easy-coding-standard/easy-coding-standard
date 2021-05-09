<?php

namespace Symplify\RuleDocGenerator\ValueObject\CodeSample;

use Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;

final class ComposerJsonAwareCodeSample extends AbstractCodeSample
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
        $badCode = (string) $badCode;
        $goodCode = (string) $goodCode;
        $composerJson = (string) $composerJson;
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
