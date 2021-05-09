<?php

namespace Nette\Localization;


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


interface_exists(Nette\Localization\ITranslator::class);
