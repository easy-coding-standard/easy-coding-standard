<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\EventDispatcher;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symplify\EasyCodingStandard\SniffRunner\EventDispatcher\Event\CheckFileTokenEvent;

final class SniffDispatcher extends EventDispatcher
{
    /**
     * @param Sniff[] $sniffs
     */
    public function addSniffListeners(array $sniffs)
    {
        foreach ($sniffs as $sniff) {
            foreach ($sniff->register() as $token) {
                $this->addTokenSniffListener($token, $sniff);
            }
        }
    }

    /**
     * @param int|string $token
     * @param Sniff $sniffObject
     */
    private function addTokenSniffListener($token, Sniff $sniffObject)
    {
        $this->addListener(
            $token,
            function (CheckFileTokenEvent $checkFileToken) use ($sniffObject) {
                $sniffObject->process(
                    $checkFileToken->getFile(),
                    $checkFileToken->getStackPointer()
                );
            }
        );
    }
}
