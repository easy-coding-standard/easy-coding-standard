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
    private $tokenListeners = [];

    public function __construct(Skipper $skipper)
    {
        $this->skipper = $skipper;
    }

    public function addSingleSniffListener(Sniff $sniff): void
    {
        $this->tokenListeners = [];
        foreach ($sniff->register() as $token) {
            $this->tokenListeners[$token][] = $sniff;
        }
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
     */
    public function dispatchToken($token, FileTokenEvent $fileTokenEvent): void
    {
        $tokenListeners = $this->tokenListeners[$token] ?? [];
        if (! count($tokenListeners)) {
            return;
        }

        $file = $fileTokenEvent->getFile();

        foreach ($tokenListeners as $sniff) {
            if ($this->skipper->shouldSkipCheckerAndFile($sniff, $file->getFilename())) {
                return;
            }

            $sniff->process($file, $fileTokenEvent->getPosition());
        }
    }
}
