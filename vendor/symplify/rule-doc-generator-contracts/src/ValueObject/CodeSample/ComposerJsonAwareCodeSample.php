<?php

declare (strict_types=1);
namespace ECSPrefix202408\Symplify\RuleDocGenerator\ValueObject\CodeSample;

use ECSPrefix202408\Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
final class ComposerJsonAwareCodeSample extends AbstractCodeSample
{
    /**
     * @readonly
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
