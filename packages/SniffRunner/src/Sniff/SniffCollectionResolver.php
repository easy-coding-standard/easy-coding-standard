<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Sniff;

use Symplify\EasyCodingStandard\SniffRunner\Repository\SniffRepository;

final class SniffCollectionResolver
{
    /**
     * @var SniffRepository
     */
    private $sniffRepository;

    public function __construct(SniffRepository $sniffRepository)
    {
        $this->sniffRepository = $sniffRepository;
    }

    /**
     * @return string[]
     */
    public function resolve(array $sniffs) : array
    {
        dump($sniffs);
        $this->sniffRepository->getByClass($sniffs);
        die;

        return $sniffClasses;
    }
}
