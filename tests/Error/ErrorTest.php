<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Error\Error;

final class ErrorTest extends TestCase
{
    public function test(): void
    {
        $error = Error::createFromLineMessageSourceClassAndFixable(1, 'message', 'class', true);
        $this->assertSame(1, $error->getLine());
        $this->assertSame('message', $error->getMessage());
        $this->assertSame('class', $error->getSourceClass());
        $this->assertTrue($error->isFixable());
    }
}
