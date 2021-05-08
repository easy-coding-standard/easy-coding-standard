<?php

namespace Symplify\SetConfigResolver\Exception;

use Exception;
final class SetNotFoundException extends \Exception
{
    /**
     * @var string
     */
    private $setName;
    /**
     * @var string[]
     */
    private $availableSetNames = [];
    /**
     * @param string[] $availableSetNames
     * @param string $message
     */
    public function __construct($message, string $setName, array $availableSetNames)
    {
        if (\is_object($message)) {
            $message = (string) $message;
        }
        $this->setName = $setName;
        $this->availableSetNames = $availableSetNames;
        parent::__construct($message);
    }
    /**
     * @return string
     */
    public function getSetName()
    {
        return $this->setName;
    }
    /**
     * @return mixed[]
     */
    public function getAvailableSetNames()
    {
        return $this->availableSetNames;
    }
}
