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
namespace ECSPrefix20210803\PharIo\Manifest;

class CopyrightInformation
{
    /** @var AuthorCollection */
    private $authors;
    /** @var License */
    private $license;
    public function __construct(\ECSPrefix20210803\PharIo\Manifest\AuthorCollection $authors, \ECSPrefix20210803\PharIo\Manifest\License $license)
    {
        $this->authors = $authors;
        $this->license = $license;
    }
    public function getAuthors() : \ECSPrefix20210803\PharIo\Manifest\AuthorCollection
    {
        return $this->authors;
    }
    public function getLicense() : \ECSPrefix20210803\PharIo\Manifest\License
    {
        return $this->license;
    }
}
