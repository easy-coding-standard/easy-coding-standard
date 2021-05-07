<?php

namespace ECSPrefix20210507\Nette\Localization;

/**
 * Translator adapter.
 */
interface Translator
{
    /**
     * Translates the given string.
     * @param  mixed  $message
     * @param  mixed  ...$parameters
     * @return string
     */
    function translate($message, ...$parameters);
}
\interface_exists(\ECSPrefix20210507\Nette\Localization\Nette\Localization\ITranslator::class);
