<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\UnusedPublic\ValueObject;

final class MethodCallReference
{
    /**
     * @readonly
     * @var string
     */
    private $class;
    /**
     * @readonly
     * @var string
     */
    private $method;
    /**
     * @readonly
     * @var bool
     */
    private $isLocal;
    public function __construct(string $class, string $method, bool $isLocal)
    {
        $this->class = $class;
        $this->method = $method;
        $this->isLocal = $isLocal;
    }
    public function getClass(): string
    {
        return $this->class;
    }
    public function getMethod(): string
    {
        return $this->method;
    }
    public function isLocal(): bool
    {
        return $this->isLocal;
    }
}
