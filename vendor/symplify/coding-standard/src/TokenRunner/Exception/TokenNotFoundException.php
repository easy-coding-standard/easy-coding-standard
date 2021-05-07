<?php

namespace Symplify\CodingStandard\TokenRunner\Exception;

use Exception;
final class TokenNotFoundException extends \Exception
{
    /**
     * @param int $position
     */
    public function __construct($position)
    {
        $message = \sprintf('Token on position %d was not found', $position);
        parent::__construct($message);
    }
}
