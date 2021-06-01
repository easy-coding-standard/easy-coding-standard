<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace ConfigTransformer20210601\Nette\Utils;

use ConfigTransformer20210601\Nette;
if (\false) {
    /** @deprecated use Nette\HtmlStringable */
    interface IHtmlString extends \ConfigTransformer20210601\Nette\HtmlStringable
    {
    }
} elseif (!\interface_exists(\ConfigTransformer20210601\Nette\Utils\IHtmlString::class)) {
    \class_alias(\ConfigTransformer20210601\Nette\HtmlStringable::class, \ConfigTransformer20210601\Nette\Utils\IHtmlString::class);
}
namespace ConfigTransformer20210601\Nette\Localization;

if (\false) {
    /** @deprecated use Nette\Localization\Translator */
    interface ITranslator extends \ConfigTransformer20210601\Nette\Localization\Translator
    {
    }
} elseif (!\interface_exists(\ConfigTransformer20210601\Nette\Localization\ITranslator::class)) {
    \class_alias(\ConfigTransformer20210601\Nette\Localization\Translator::class, \ConfigTransformer20210601\Nette\Localization\ITranslator::class);
}
