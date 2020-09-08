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
     * @see https://regex101.com/r/SZr0X5/4
     * @var string
     */
    protected const PHP_CODE_SNIPPET = '#(?<opening><<<(\'?([A-Z]+)\'?|\"?([A-Z]+)\"?)\s+|(\'?([A-Z]+)\'?|\"?([A-Z]+)\"?)\s+)(?<content>[^\3|\4]+\n)(?<closing>(\s+)?\3|\4)#ms';
}
