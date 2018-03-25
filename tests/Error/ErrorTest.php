<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Error\ErrorFactory;

final class ErrorTest extends TestCase
{
    public function test(): void
    {
        $error = (new ErrorFactory())->createFromLineMessageSourceClass(1, 'message', 'class');

        $this->assertSame(1, $error->getLine());
        $this->assertSame('message', $error->getMessage());
        $this->assertSame('class', $error->getSourceClass());
    }
}
