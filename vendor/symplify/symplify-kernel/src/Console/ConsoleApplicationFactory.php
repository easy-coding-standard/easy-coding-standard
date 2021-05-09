<?php

namespace Symplify\SymplifyKernel\Console;

use ECSPrefix20210509\Symfony\Component\Console\Command\Command;
final class ConsoleApplicationFactory
{
    /**
     * @var Command[]
     */
    private $commands = [];
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->commands = $commands;
    }
    /**
     * @return \Symplify\SymplifyKernel\Console\AutowiredConsoleApplication
     */
    public function create()
    {
        return new \Symplify\SymplifyKernel\Console\AutowiredConsoleApplication($this->commands);
    }
}
