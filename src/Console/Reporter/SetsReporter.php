<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Reporter;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SetsReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    public function __construct(SymfonyStyle $symfonyStyle, ParameterProvider $parameterProvider)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->parameterProvider = $parameterProvider;
    }

    public function report(): void
    {
        $sets = (array) $this->parameterProvider->provideParameter(Option::SETS);
        if ($sets === []) {
            return;
        }

        $this->symfonyStyle->title('Loaded Sets');

        sort($sets);

        foreach ($sets as $set) {
            $setFileInfo = new SmartFileInfo($set);
            $filename = $setFileInfo->getRelativeFilePathFromCwd();
            $this->symfonyStyle->writeln(' * ' . $filename);
        }

        $message = sprintf('%d loaded sets', count($sets));
        $this->symfonyStyle->success($message);
    }
}
