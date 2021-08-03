<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration;

use function sprintf;
use DOMDocument;
use DOMElement;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class RemoveEmptyFilter implements \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Migration
{
    /**
     * @throws MigrationException
     */
    public function migrate(\DOMDocument $document) : void
    {
        $whitelist = $document->getElementsByTagName('whitelist')->item(0);
        if ($whitelist instanceof \DOMElement) {
            $this->ensureEmpty($whitelist);
            $whitelist->parentNode->removeChild($whitelist);
        }
        $filter = $document->getElementsByTagName('filter')->item(0);
        if ($filter instanceof \DOMElement) {
            $this->ensureEmpty($filter);
            $filter->parentNode->removeChild($filter);
        }
    }
    /**
     * @throws MigrationException
     */
    private function ensureEmpty(\DOMElement $element) : void
    {
        if ($element->attributes->length > 0) {
            throw new \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\MigrationException(\sprintf('%s element has unexpected attributes', $element->nodeName));
        }
        if ($element->getElementsByTagName('*')->length > 0) {
            throw new \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\MigrationException(\sprintf('%s element has unexpected children', $element->nodeName));
        }
    }
}
