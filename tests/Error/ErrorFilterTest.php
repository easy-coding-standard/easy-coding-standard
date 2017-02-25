<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\Error;

use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;
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

    protected function setUp(): void
    {
        $this->errorFilter = new ErrorFilter(new ConfigurationNormalizer);
        $this->errors['someFile'][] = $this->createError();
        $this->errors['anotherFile'][] = $this->createAnotherError();
    }

    public function testAsterixFilterOut(): void
    {
        $this->errorFilter->setIgnoredErrors(['packages/CodingStandard/src/Sniffs/*/*Sniff.php']);

        $this->errors = [];
        $this->errors['packages/CodingStandard/src/Sniffs/WhiteSpace/PropertiesMethodsMutualSpacingSniff.php'][]
            = $this->createError();
        $this->assertCount(1, $this->errors);

        $filteredErrors = $this->errorFilter->filterOutIgnoredErrors($this->errors);
        $this->assertCount(0, $filteredErrors);
    }

    public function testEmptyFilterOutIgnoredErrors(): void
    {
        $filteredErrors = $this->errorFilter->filterOutIgnoredErrors($this->errors);

        $this->assertCount(2, $filteredErrors);
    }

    public function testFilterOutIgnoredErrorsWholeFile(): void
    {
        $this->errorFilter->setIgnoredErrors(['someFile']);
        $filteredErrors = $this->errorFilter->filterOutIgnoredErrors($this->errors);

        $this->assertCount(1, $filteredErrors);

        $this->errorFilter->setIgnoredErrors(['someFile' => []]);
        $filteredErrorsWithAnotherConfiguration = $this->errorFilter->filterOutIgnoredErrors($this->errors);

        $this->errorFilter->setIgnoredErrors(['some*']);
        $filteredErrorsWithAsterixConfiguration = $this->errorFilter->filterOutIgnoredErrors($this->errors);

        $this->assertSame($filteredErrors, $filteredErrorsWithAnotherConfiguration);
        $this->assertSame($filteredErrors, $filteredErrorsWithAsterixConfiguration);
    }

    public function testFilterOutIgnoredErrorFileWithOneClass(): void
    {
        $this->errorFilter->setIgnoredErrors(['someFile' => [stdClass::class]]);

        $filteredErrors = $this->errorFilter->filterOutIgnoredErrors($this->errors);
        $this->assertCount(1, $filteredErrors);
    }

    private function createError(): Error
    {
        return new Error(5, 'Fail', stdClass::class, false);
    }

    private function createAnotherError(): Error
    {
        return new Error(5, 'Fail', stdClass::class . '2', false);
    }
}
