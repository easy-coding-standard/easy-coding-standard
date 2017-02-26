<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\TokenDispatcher;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\TokenDispatcher\Event\FileTokenEvent;

final class TokenDispatcher
{
    /**
     * @var Skipper
     */
    private $skipper;

    /**
     * @var Sniff[][]
     */
    private $tokenListeners;

    public function __construct(Skipper $skipper)
    {
        $this->skipper = $skipper;
    }

    /**
     * @param Sniff[] $sniffs
     */
    public function addSniffListeners(array $sniffs): void
    {
        foreach ($sniffs as $sniff) {
            foreach ($sniff->register() as $token) {
                $this->tokenListeners[$token][] = $sniff;
            }
        }
    }

    /**
     * @param int|string $token
     * @param FileTokenEvent $fileTokenEvent
     */
    public function dispatchToken($token, FileTokenEvent $fileTokenEvent): void
    {
        $tokenListeners = $this->tokenListeners[$token] ?? [];

        foreach ($tokenListeners as $sniff) {
            $filename = $fileTokenEvent->getFile()
                ->getFilename();

            if ($this->skipper->shouldSkipSourceClassAndFile($sniff, $filename)) {
                return;
            }

            $sniff->process($fileTokenEvent->getFile(), $fileTokenEvent->getPosition());
        }
    }
}
