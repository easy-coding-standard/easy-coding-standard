<?php

declare (strict_types=1);
namespace ECSPrefix20210727\Symplify\RuleDocGenerator\ValueObject\CodeSample;

use ECSPrefix20210727\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
final class ComposerJsonAwareCodeSample extends \ECSPrefix20210727\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample
{
    /**
     * @var string
     */
    private $composerJson;
    public function __construct(string $badCode, string $goodCode, string $composerJson)
    {
        $this->composerJson = $composerJson;
        parent::__construct($badCode, $goodCode);
    }
    public function getComposerJson() : string
    {
        return $this->composerJson;
    }
}
