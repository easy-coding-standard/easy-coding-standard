<?php

namespace Symplify\SetConfigResolver;

use Symplify\SetConfigResolver\Contract\SetProviderInterface;
use Symplify\SetConfigResolver\Exception\SetNotFoundException;
use Symplify\SetConfigResolver\ValueObject\Set;
use Symplify\SmartFileSystem\SmartFileInfo;
final class SetResolver
{
    /**
     * @var SetProviderInterface
     */
    private $setProvider;
    public function __construct(\Symplify\SetConfigResolver\Contract\SetProviderInterface $setProvider)
    {
        $this->setProvider = $setProvider;
    }
    /**
     * @param string $setName
     * @return \Symplify\SmartFileSystem\SmartFileInfo
     */
    public function detectFromName($setName)
    {
        if (\is_object($setName)) {
            $setName = (string) $setName;
        }
        $set = $this->setProvider->provideByName($setName);
        if (!$set instanceof \Symplify\SetConfigResolver\ValueObject\Set) {
            $this->reportSetNotFound($setName);
        }
        return $set->getSetFileInfo();
    }
    /**
     * @return void
     * @param string $setName
     */
    private function reportSetNotFound($setName)
    {
        if (\is_object($setName)) {
            $setName = (string) $setName;
        }
        $message = \sprintf('Set "%s" was not found', $setName);
        throw new \Symplify\SetConfigResolver\Exception\SetNotFoundException($message, $setName, $this->setProvider->provideSetNames());
    }
}
