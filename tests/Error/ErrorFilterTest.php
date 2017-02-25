<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\Error;

use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorFilter;

final class ErrorFilterTest extends TestCase
{
    /**
     * @var ErrorFilter
     */
    private $errorFilter;

    /**
     * @var Error[]
     */
    private $errors;

    protected function setUp()
    {
        $this->errorFilter = new ErrorFilter();
        $this->errors['someFile'][] = $this->createError();

    }

    public function testEmptyFilterOutIgnoredErrors()
    {
        $filteredErrors = $this->errorFilter->filterOutIgnoredErrors($this->errors);

        $this->assertCount(1, $filteredErrors);
    }

    public function testFilterOutIgnoredErrors()
    {
        $this->errorFilter->setIgnoredErrors(['failed check']);
        $filteredErrors = $this->errorFilter->filterOutIgnoredErrors($this->errors);

        $this->assertCount(0, $filteredErrors);
    }

    private function createError(): Error
    {
        return new Error(5, 'Total failed check', stdClass::class, false);
    }
}
