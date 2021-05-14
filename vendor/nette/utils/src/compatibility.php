<?php

namespace ECSPrefix20210514\Nette\Utils;

use ECSPrefix20210514\Nette;
if (\false) {
    /** @deprecated use Nette\HtmlStringable */
    interface IHtmlString extends \ECSPrefix20210514\Nette\HtmlStringable
    {
    }
} elseif (!\interface_exists(\ECSPrefix20210514\Nette\Utils\IHtmlString::class)) {
    \class_alias(\ECSPrefix20210514\Nette\HtmlStringable::class, \ECSPrefix20210514\Nette\Utils\IHtmlString::class);
}
namespace ECSPrefix20210514\Nette\Localization;

if (\false) {
    /** @deprecated use Nette\Localization\Translator */
    interface ITranslator extends \ECSPrefix20210514\Nette\Localization\Translator
    {
    }
} elseif (!\interface_exists(\ECSPrefix20210514\Nette\Localization\ITranslator::class)) {
    \class_alias(\ECSPrefix20210514\Nette\Localization\Translator::class, \ECSPrefix20210514\Nette\Localization\ITranslator::class);
}
