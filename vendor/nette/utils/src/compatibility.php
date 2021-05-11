<?php

namespace ECSPrefix20210511\Nette\Utils;

use ECSPrefix20210511\Nette;
if (\false) {
    /** @deprecated use Nette\HtmlStringable */
    interface IHtmlString extends \ECSPrefix20210511\Nette\HtmlStringable
    {
    }
} elseif (!\interface_exists(\ECSPrefix20210511\Nette\Utils\IHtmlString::class)) {
    \class_alias(\ECSPrefix20210511\Nette\HtmlStringable::class, \ECSPrefix20210511\Nette\Utils\IHtmlString::class);
}
namespace ECSPrefix20210511\Nette\Localization;

if (\false) {
    /** @deprecated use Nette\Localization\Translator */
    interface ITranslator extends \ECSPrefix20210511\Nette\Localization\Translator
    {
    }
} elseif (!\interface_exists(\ECSPrefix20210511\Nette\Localization\ITranslator::class)) {
    \class_alias(\ECSPrefix20210511\Nette\Localization\Translator::class, \ECSPrefix20210511\Nette\Localization\ITranslator::class);
}
