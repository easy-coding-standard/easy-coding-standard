<?php

namespace ECSPrefix20210517\Symplify\SetConfigResolver\Exception;

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
     * @param string $setName
     */
    public function __construct($message, $setName, array $availableSetNames)
    {
        $message = (string) $message;
        $setName = (string) $setName;
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
