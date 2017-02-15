<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Repository;

use Symplify\EasyCodingStandard\SniffRunner\Sniff\Finder\SniffFinder;

final class SniffRepository
{
    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    /**
     * @var string[][]
     */
    private $sniffClasses;

    public function __construct(SniffFinder $sniffFinder)
    {
        $this->sniffFinder = $sniffFinder;
    }

    public function getByClass(string $class)
    {
        $this->init();
        return $this->sniffClasses[$class];
    }

    private function init(): void
    {
        if (count($this->sniffClasses)) {
            return;
        }

        $sniffClasses = $this->sniffFinder->findAllSniffClasses();
        foreach ($sniffClasses as $sniffClass) {
            $this->sniffClasses[$sniffClass] = $sniffClass;
        }
    }
}
