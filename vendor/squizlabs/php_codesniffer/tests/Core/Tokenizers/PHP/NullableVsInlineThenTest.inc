<?php

abstract class Nullable
{
    /* testNullableReadonlyOnly */
    readonly ?int $prop;

    /* testNullablePrivateSet */
    private(set) ?int $prop2;

    /* testNullablePublicProtectedSet */
    public protected(set) ?int $prop3;

    /* testNullableFinalOnly */
    final ?bool $prop4;

    /* testNullableAbstractOnly */
    abstract ?string $prop5 { get; }
}

class InlineThen
{
    /* testInlineThenInPropertyDefaultValue */
    public int $prop = self::SOMECONT ? PHP_CONST ? OTHER_CONST;
}
