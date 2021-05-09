<?php

namespace Nette;


interface HtmlStringable
{
	/**
	 * Returns string in HTML format
	 * @return string
	 */
	function __toString();
}


interface_exists(Utils\IHtmlString::class);
