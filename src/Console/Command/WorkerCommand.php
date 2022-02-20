<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20220220\Clue\React\NDJson\Decoder;
use ECSPrefix20220220\Clue\React\NDJson\Encoder;
use ECSPrefix20220220\React\EventLoop\StreamSelectLoop;
use ECSPrefix20220220\React\Socket\ConnectionInterface;
use ECSPrefix20220220\React\Socket\TcpConnector;
use ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\MemoryLimitter;
use Symplify\EasyCodingStandard\Parallel\WorkerRunner;
use ECSPrefix20220220\Symplify\EasyParallel\Enum\Action;
use ECSPrefix20220220\Symplify\EasyParallel\Enum\ReactCommand;
/**
 * Inspired at: https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92
 * https://github.com/phpstan/phpstan-src/blob/c471c7b050e0929daf432288770de673b394a983/src/Command/WorkerCommand.php
 *
 * ↓↓↓
 * https://github.com/phpstan/phpstan-src/commit/b84acd2e3eadf66189a64fdbc6dd18ff76323f67#diff-7f625777f1ce5384046df08abffd6c911cfbb1cfc8fcb2bdeaf78f337689e3e2
 */
final class WorkerCommand extends \Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand
{
    /**
     * @var \Symplify\EasyCodingStandard\Parallel\WorkerRunner
     */
    private $workerRunner;
    /**
     * @var \Symplify\EasyCodingStandard\MemoryLimitter
     */
    private $memoryLimitter;
    public function __construct(\Symplify\EasyCodingStandard\Parallel\WorkerRunner $workerRunner, \Symplify\EasyCodingStandard\MemoryLimitter $memoryLimitter)
    {
        $this->workerRunner = $workerRunner;
        $this->memoryLimitter = $memoryLimitter;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName('worker');
        $this->setDescription('(Internal) Support for parallel process');
        parent::configure();
    }
    protected function execute(\ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20220220\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $configuration = $this->configurationFactory->createFromInput($input);
        $this->memoryLimitter->adjust($configuration);
        $streamSelectLoop = new \ECSPrefix20220220\React\EventLoop\StreamSelectLoop();
        $parallelIdentifier = $configuration->getParallelIdentifier();
        $tcpConnector = new \ECSPrefix20220220\React\Socket\TcpConnector($streamSelectLoop);
        $promise = $tcpConnector->connect('127.0.0.1:' . $configuration->getParallelPort());
        $promise->then(function (\ECSPrefix20220220\React\Socket\ConnectionInterface $connection) use($parallelIdentifier, $configuration) : void {
            $inDecoder = new \ECSPrefix20220220\Clue\React\NDJson\Decoder($connection, \true, 512, 0);
            $outEncoder = new \ECSPrefix20220220\Clue\React\NDJson\Encoder($connection, 0);
            // handshake?
            $outEncoder->write([\ECSPrefix20220220\Symplify\EasyParallel\Enum\ReactCommand::ACTION => \ECSPrefix20220220\Symplify\EasyParallel\Enum\Action::HELLO, \ECSPrefix20220220\Symplify\EasyParallel\Enum\ReactCommand::IDENTIFIER => $parallelIdentifier]);
            $this->workerRunner->run($outEncoder, $inDecoder, $configuration);
        });
        $streamSelectLoop->run();
        return self::SUCCESS;
    }
}
