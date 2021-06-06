<?php

declare (strict_types=1);
namespace ECSPrefix20210606\Symplify\RuleDocGenerator\ValueObject\CodeSample;

use ECSPrefix20210606\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
final class ComposerJsonAwareCodeSample extends \ECSPrefix20210606\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample
{
    /**
     * @var string
     */
    private $composerJson;
    public function __construct(string $badCode, string $goodCode, string $composerJson)
    {
        parent::__construct($badCode, $goodCode);
        $this->composerJson = $composerJson;
    }
    public function getComposerJson() : string
    {
        return $this->composerJson;
    }
}
