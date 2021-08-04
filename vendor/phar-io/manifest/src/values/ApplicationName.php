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

class ApplicationName
{
    /** @var string */
    private $name;
    public function __construct(string $name)
    {
        $this->ensureValidFormat($name);
        $this->name = $name;
    }
    public function asString() : string
    {
        return $this->name;
    }
    public function isEqual(\ECSPrefix20210804\PharIo\Manifest\ApplicationName $name) : bool
    {
        return $this->name === $name->name;
    }
    private function ensureValidFormat(string $name) : void
    {
        if (!\preg_match('#\\w/\\w#', $name)) {
            throw new \ECSPrefix20210804\PharIo\Manifest\InvalidApplicationNameException(\sprintf('Format of name "%s" is not valid - expected: vendor/packagename', $name), \ECSPrefix20210804\PharIo\Manifest\InvalidApplicationNameException::InvalidFormat);
        }
    }
}
