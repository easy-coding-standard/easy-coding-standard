<?php

namespace ECSPrefix202412\Illuminate\Container\Attributes;

use Attribute;
use ECSPrefix202412\Illuminate\Contracts\Container\Container;
use ECSPrefix202412\Illuminate\Contracts\Container\ContextualAttribute;
#[Attribute(Attribute::TARGET_PARAMETER)]
class Authenticated implements ContextualAttribute
{
    /**
     * @var string|null
     */
    public $guard;
    /**
     * Create a new class instance.
     */
    public function __construct(?string $guard = null)
    {
        $this->guard = $guard;
    }
    /**
     * Resolve the currently authenticated user.
     *
     * @param  self  $attribute
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public static function resolve(self $attribute, Container $container)
    {
        return $container->make('auth')->guard($attribute->guard)->user();
    }
}
