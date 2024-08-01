<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace ECSPrefix202408\Nette\Localization;

/**
 * Translator adapter.
 */
interface Translator
{
    /**
     * Translates the given string.
     * @param string|\Stringable $message
     * @return string|\Stringable
     * @param mixed ...$parameters
     */
    function translate($message, ...$parameters);
}
\interface_exists(ITranslator::class);
