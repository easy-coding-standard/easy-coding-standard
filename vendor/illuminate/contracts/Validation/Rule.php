<?php

namespace ECSPrefix202408\Illuminate\Contracts\Validation;

/**
 * @deprecated see ValidationRule
 */
interface Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value);
    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message();
}
