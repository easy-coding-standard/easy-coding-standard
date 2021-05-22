<?php

declare (strict_types=1);
/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer;

use ECSPrefix20210522\Symfony\Contracts\EventDispatcher\Event;
/**
 * Event that is fired when file was processed by Fixer.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FixerFileProcessedEvent extends \ECSPrefix20210522\Symfony\Contracts\EventDispatcher\Event
{
    /**
     * Event name.
     */
    const NAME = 'fixer.file_processed';
    const STATUS_UNKNOWN = 0;
    const STATUS_INVALID = 1;
    const STATUS_SKIPPED = 2;
    const STATUS_NO_CHANGES = 3;
    const STATUS_FIXED = 4;
    const STATUS_EXCEPTION = 5;
    const STATUS_LINT = 6;
    /**
     * @var int
     */
    private $status;
    public function __construct(int $status)
    {
        $this->status = $status;
    }
    public function getStatus() : int
    {
        return $this->status;
    }
}
