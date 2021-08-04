<?php

declare (strict_types=1);
/*
 * This file is part of PharIo\Manifest.
 *
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\PharIo\Manifest;

class ManifestLoader
{
    public static function fromFile(string $filename) : \ECSPrefix20210804\PharIo\Manifest\Manifest
    {
        try {
            return (new \ECSPrefix20210804\PharIo\Manifest\ManifestDocumentMapper())->map(\ECSPrefix20210804\PharIo\Manifest\ManifestDocument::fromFile($filename));
        } catch (\ECSPrefix20210804\PharIo\Manifest\Exception $e) {
            throw new \ECSPrefix20210804\PharIo\Manifest\ManifestLoaderException(\sprintf('Loading %s failed.', $filename), (int) $e->getCode(), $e);
        }
    }
    public static function fromPhar(string $filename) : \ECSPrefix20210804\PharIo\Manifest\Manifest
    {
        return self::fromFile('phar://' . $filename . '/manifest.xml');
    }
    public static function fromString(string $manifest) : \ECSPrefix20210804\PharIo\Manifest\Manifest
    {
        try {
            return (new \ECSPrefix20210804\PharIo\Manifest\ManifestDocumentMapper())->map(\ECSPrefix20210804\PharIo\Manifest\ManifestDocument::fromString($manifest));
        } catch (\ECSPrefix20210804\PharIo\Manifest\Exception $e) {
            throw new \ECSPrefix20210804\PharIo\Manifest\ManifestLoaderException('Processing string failed', (int) $e->getCode(), $e);
        }
    }
}
