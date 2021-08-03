<?php

declare (strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\SebastianBergmann\CodeCoverage\Report\Xml;

use DOMElement;
use ECSPrefix20210803\TheSeer\Tokenizer\NamespaceUri;
use ECSPrefix20210803\TheSeer\Tokenizer\Tokenizer;
use ECSPrefix20210803\TheSeer\Tokenizer\XMLSerializer;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Source
{
    /** @var DOMElement */
    private $context;
    public function __construct(\DOMElement $context)
    {
        $this->context = $context;
    }
    public function setSourceCode(string $source) : void
    {
        $context = $this->context;
        $tokens = (new \ECSPrefix20210803\TheSeer\Tokenizer\Tokenizer())->parse($source);
        $srcDom = (new \ECSPrefix20210803\TheSeer\Tokenizer\XMLSerializer(new \ECSPrefix20210803\TheSeer\Tokenizer\NamespaceUri($context->namespaceURI)))->toDom($tokens);
        $context->parentNode->replaceChild($context->ownerDocument->importNode($srcDom->documentElement, \true), $context);
    }
}
