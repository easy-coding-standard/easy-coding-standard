<?php

namespace ECSPrefix20210507\Nette\Utils;

use ECSPrefix20210507\Nette;
/**
 * Nette\Object behaviour mixin.
 * @deprecated
 */
final class ObjectMixin
{
    use Nette\StaticClass;
    /** @deprecated  use ObjectHelpers::getSuggestion()
     * @return string|null
     * @param string $value */
    public static function getSuggestion(array $possibilities, $value)
    {
        \trigger_error(__METHOD__ . '() has been renamed to Nette\\Utils\\ObjectHelpers::getSuggestion()', \E_USER_DEPRECATED);
        return \ECSPrefix20210507\Nette\Utils\ObjectHelpers::getSuggestion($possibilities, $value);
    }
    /**
     * @return void
     */
    public static function setExtensionMethod()
    {
        \trigger_error('Class Nette\\Utils\\ObjectMixin is deprecated', \E_USER_DEPRECATED);
    }
    /**
     * @return void
     */
    public static function getExtensionMethod()
    {
        \trigger_error('Class Nette\\Utils\\ObjectMixin is deprecated', \E_USER_DEPRECATED);
    }
}
