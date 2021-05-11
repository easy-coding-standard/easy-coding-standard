<?php

namespace ECSPrefix20210511\Nette\Localization;

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
\interface_exists(\ECSPrefix20210511\Nette\Localization\Nette\Localization\ITranslator::class);
