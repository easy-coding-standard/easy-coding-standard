<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\HeredocNowdoc;

use Symplify\EasyCodingStandard\Formatter\AbstractPHPFormatter;

/**
 * @see \Symplify\EasyCodingStandard\Tests\HeredocNowdoc\HeredocNowdocPHPCodeFormatterTest
 */
final class HeredocNowdocPHPCodeFormatter extends AbstractPHPFormatter
{
    /**
     * @see https://regex101.com/r/SZr0X5/12
     * @var string
     */
    private const PHP_CODE_SNIPPET = '#(?<opening><<<(\'?([A-Z]+)\'?|\"?([A-Z]+)\"?)\s+)(?<content>[^\3|\4]+)(?<closing>(\s+)?\3|\4)#msU';

    public function provideRegex(): string
    {
        return self::PHP_CODE_SNIPPET;
    }
}
