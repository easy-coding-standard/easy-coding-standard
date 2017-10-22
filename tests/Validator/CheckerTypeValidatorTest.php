<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Validator;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Arrays\ArrayDeclarationSniff;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\EasyCodingStandard\Exception\Validator\CheckerIsNotSupportedException;
use Symplify\EasyCodingStandard\Validator\CheckerTypeValidator;

final class CheckerTypeValidatorTest extends TestCase
{
    /**
     * @var CheckerTypeValidator
     */
    private $checkerTypeValidator;

    protected function setUp(): void
    {
        $this->checkerTypeValidator = new CheckerTypeValidator;
    }

    public function test(): void
    {
        $location = 'location';
        $this->checkerTypeValidator->validate([ArraySyntaxFixer::class], $location);
        $this->checkerTypeValidator->validate([ArrayDeclarationSniff::class], $location);

        $this->expectException(CheckerIsNotSupportedException::class);
        $this->expectExceptionMessage(sprintf(
            'Checker "%s" was not found or is not supported. Use class that implements any of %s or %s. '
                . 'Invalid checker defined in %s',
            stdClass::class,
            Sniff::class,
            FixerInterface::class,
            $location
        ));
        $this->checkerTypeValidator->validate([stdClass::class], $location);
    }
}
