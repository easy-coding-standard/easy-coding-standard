<?php

namespace ECSPrefix20210804\Prophecy\Doubler\Generator\Node;

use ECSPrefix20210804\Prophecy\Exception\Doubler\DoubleException;
final class ReturnTypeNode extends \ECSPrefix20210804\Prophecy\Doubler\Generator\Node\TypeNodeAbstract
{
    protected function getRealType(string $type) : string
    {
        if ($type == 'void') {
            return $type;
        }
        return parent::getRealType($type);
    }
    protected function guardIsValidType()
    {
        if (isset($this->types['void']) && \count($this->types) !== 1) {
            throw new \ECSPrefix20210804\Prophecy\Exception\Doubler\DoubleException('void cannot be part of a union');
        }
        parent::guardIsValidType();
    }
    public function isVoid() : bool
    {
        return $this->types == ['void' => 'void'];
    }
}
