<?php

namespace ECSPrefix202307\Illuminate\Contracts\Validation;

use ECSPrefix202307\Illuminate\Validation\Validator;
interface ValidatorAwareRule
{
    /**
     * Set the current validator.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator(Validator $validator);
}
