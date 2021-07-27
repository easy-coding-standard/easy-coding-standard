<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace ECSPrefix20210727\Nette\Utils;

use ECSPrefix20210727\Nette;
if (\false) {
    /** @deprecated use Nette\HtmlStringable */
    interface IHtmlString extends \ECSPrefix20210727\Nette\HtmlStringable
    {
    }
} elseif (!\interface_exists(\ECSPrefix20210727\Nette\Utils\IHtmlString::class)) {
    \class_alias(\ECSPrefix20210727\Nette\HtmlStringable::class, \ECSPrefix20210727\Nette\Utils\IHtmlString::class);
}
namespace ECSPrefix20210727\Nette\Localization;

if (\false) {
    /** @deprecated use Nette\Localization\Translator */
    interface ITranslator extends \ECSPrefix20210727\Nette\Localization\Translator
    {
    }
} elseif (!\interface_exists(\ECSPrefix20210727\Nette\Localization\ITranslator::class)) {
    \class_alias(\ECSPrefix20210727\Nette\Localization\Translator::class, \ECSPrefix20210727\Nette\Localization\ITranslator::class);
}
