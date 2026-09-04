<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\ValueObject;

final class LocalAndExternalMethodCallReferences
{
    /**
     * @var string[]
     * @readonly
     */
    private $localMethodCallReferences;
    /**
     * @var string[]
     * @readonly
     */
    private $externalMethodCallReferences;
    /**
     * @param string[] $localMethodCallReferences
     * @param string[] $externalMethodCallReferences
     */
    public function __construct(array $localMethodCallReferences, array $externalMethodCallReferences)
    {
        $this->localMethodCallReferences = $localMethodCallReferences;
        $this->externalMethodCallReferences = $externalMethodCallReferences;
    }
    /**
     * @return string[]
     */
    public function getLocalMethodCallReferences(): array
    {
        return $this->localMethodCallReferences;
    }
    /**
     * @return string[]
     */
    public function getExternalMethodCallReferences(): array
    {
        return $this->externalMethodCallReferences;
    }
}
