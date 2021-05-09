<?php

namespace Nette\Utils;

use Nette;

if (false) {
	/** @deprecated use Nette\HtmlStringable */
	interface IHtmlString extends Nette\HtmlStringable
	{
	}
} elseif (!interface_exists(IHtmlString::class)) {
	class_alias(Nette\HtmlStringable::class, IHtmlString::class);
}

namespace Nette\Localization;

if (false) {
	/** @deprecated use Nette\Localization\Translator */
	interface ITranslator extends Translator
	{
	}
} elseif (!interface_exists(ITranslator::class)) {
	class_alias(Translator::class, ITranslator::class);
}
