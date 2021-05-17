<?php

declare (strict_types=1);
namespace ECSPrefix20210517\Symplify\SetConfigResolver;

use ECSPrefix20210517\Symplify\SetConfigResolver\Contract\SetProviderInterface;
use ECSPrefix20210517\Symplify\SetConfigResolver\Exception\SetNotFoundException;
use ECSPrefix20210517\Symplify\SetConfigResolver\ValueObject\Set;
use ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo;
final class SetResolver
{
    /**
     * @var SetProviderInterface
     */
    private $setProvider;
    public function __construct(\ECSPrefix20210517\Symplify\SetConfigResolver\Contract\SetProviderInterface $setProvider)
    {
        $this->setProvider = $setProvider;
    }
    public function detectFromName(string $setName) : \ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo
    {
        $set = $this->setProvider->provideByName($setName);
        if (!$set instanceof \ECSPrefix20210517\Symplify\SetConfigResolver\ValueObject\Set) {
            $this->reportSetNotFound($setName);
        }
        return $set->getSetFileInfo();
    }
    /**
     * @return void
     */
    private function reportSetNotFound(string $setName)
    {
        $message = \sprintf('Set "%s" was not found', $setName);
        throw new \ECSPrefix20210517\Symplify\SetConfigResolver\Exception\SetNotFoundException($message, $setName, $this->setProvider->provideSetNames());
    }
}
