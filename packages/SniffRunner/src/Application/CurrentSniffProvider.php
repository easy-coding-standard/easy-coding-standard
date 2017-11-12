<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Application;

use PHP_CodeSniffer\Sniffs\Sniff;

final class CurrentSniffProvider
{
    /**
     * @var Sniff|null
     */
    private $sniff;

    public function setSniff(Sniff $sniff): void
    {
        $this->sniff = $sniff;
    }

    public function getSniffClass(): ?string
    {
        if ($this->sniff) {
            return get_class($this->sniff);
        }

        return null;
    }
}
