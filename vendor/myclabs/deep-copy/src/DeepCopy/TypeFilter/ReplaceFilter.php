<?php

namespace ECSPrefix20210803\DeepCopy\TypeFilter;

/**
 * @final
 */
class ReplaceFilter implements \ECSPrefix20210803\DeepCopy\TypeFilter\TypeFilter
{
    /**
     * @var callable
     */
    protected $callback;
    /**
     * @param callable $callable Will be called to get the new value for each element to replace
     */
    public function __construct(callable $callable)
    {
        $this->callback = $callable;
    }
    /**
     * {@inheritdoc}
     */
    public function apply($element)
    {
        return \call_user_func($this->callback, $element);
    }
}
