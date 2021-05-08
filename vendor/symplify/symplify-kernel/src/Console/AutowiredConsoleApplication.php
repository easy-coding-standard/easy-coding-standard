<?php

namespace Symplify\SymplifyKernel\Console;

use ECSPrefix20210508\Symfony\Component\Console\Command\Command;
/**
 * @see \Symplify\SymplifyKernel\Tests\Console\AbstractSymplifyConsoleApplication\AutowiredConsoleApplicationTest
 */
final class AutowiredConsoleApplication extends \Symplify\SymplifyKernel\Console\AbstractSymplifyConsoleApplication
{
    /**
     * @param Command[] $commands
     * @param string $name
     * @param string $version
     */
    public function __construct(array $commands, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        $name = (string) $name;
        $version = (string) $version;
        parent::__construct($commands, $name, $version);
    }
}
