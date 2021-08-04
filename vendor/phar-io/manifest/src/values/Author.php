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

class Author
{
    /** @var string */
    private $name;
    /** @var Email */
    private $email;
    public function __construct(string $name, \ECSPrefix20210804\PharIo\Manifest\Email $email)
    {
        $this->name = $name;
        $this->email = $email;
    }
    public function asString() : string
    {
        return \sprintf('%s <%s>', $this->name, $this->email->asString());
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getEmail() : \ECSPrefix20210804\PharIo\Manifest\Email
    {
        return $this->email;
    }
}
