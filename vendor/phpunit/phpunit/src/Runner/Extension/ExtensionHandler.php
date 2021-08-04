<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\PHPUnit\Runner\Extension;

use function class_exists;
use function sprintf;
use ECSPrefix20210804\PHPUnit\Framework\TestListener;
use ECSPrefix20210804\PHPUnit\Runner\Exception;
use ECSPrefix20210804\PHPUnit\Runner\Hook;
use ECSPrefix20210804\PHPUnit\TextUI\TestRunner;
use ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Extension;
use ReflectionClass;
use ReflectionException;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ExtensionHandler
{
    /**
     * @throws Exception
     */
    public function registerExtension(\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Extension $extensionConfiguration, \ECSPrefix20210804\PHPUnit\TextUI\TestRunner $runner) : void
    {
        $extension = $this->createInstance($extensionConfiguration);
        if (!$extension instanceof \ECSPrefix20210804\PHPUnit\Runner\Hook) {
            throw new \ECSPrefix20210804\PHPUnit\Runner\Exception(\sprintf('Class "%s" does not implement a PHPUnit\\Runner\\Hook interface', $extensionConfiguration->className()));
        }
        $runner->addExtension($extension);
    }
    /**
     * @throws Exception
     *
     * @deprecated
     */
    public function createTestListenerInstance(\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Extension $listenerConfiguration) : \ECSPrefix20210804\PHPUnit\Framework\TestListener
    {
        $listener = $this->createInstance($listenerConfiguration);
        if (!$listener instanceof \ECSPrefix20210804\PHPUnit\Framework\TestListener) {
            throw new \ECSPrefix20210804\PHPUnit\Runner\Exception(\sprintf('Class "%s" does not implement the PHPUnit\\Framework\\TestListener interface', $listenerConfiguration->className()));
        }
        return $listener;
    }
    /**
     * @throws Exception
     */
    private function createInstance(\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Extension $extensionConfiguration) : object
    {
        $this->ensureClassExists($extensionConfiguration);
        try {
            $reflector = new \ReflectionClass($extensionConfiguration->className());
        } catch (\ReflectionException $e) {
            throw new \ECSPrefix20210804\PHPUnit\Runner\Exception($e->getMessage(), (int) $e->getCode(), $e);
        }
        if (!$extensionConfiguration->hasArguments()) {
            return $reflector->newInstance();
        }
        return $reflector->newInstanceArgs($extensionConfiguration->arguments());
    }
    /**
     * @throws Exception
     */
    private function ensureClassExists(\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Extension $extensionConfiguration) : void
    {
        if (\class_exists($extensionConfiguration->className(), \false)) {
            return;
        }
        if ($extensionConfiguration->hasSourceFile()) {
            /**
             * @noinspection PhpIncludeInspection
             * @psalm-suppress UnresolvableInclude
             */
            require_once $extensionConfiguration->sourceFile();
        }
        if (!\class_exists($extensionConfiguration->className())) {
            throw new \ECSPrefix20210804\PHPUnit\Runner\Exception(\sprintf('Class "%s" does not exist', $extensionConfiguration->className()));
        }
    }
}
