<?php

class Nullable
{
    /* testNullableReadonlyOnly */
    readonly ?int $prop;
}

class InlineThen
{
    /* testInlineThenInPropertyDefaultValue */
    public int $prop = self::SOMECONT ? PHP_CONST ? OTHER_CONST;
}
