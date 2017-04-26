<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Tests;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Sniffs\Classes\FinalInterfaceSniff;
use Symplify\EasyCodingStandard\Configuration\CheckerFilter;

final class CheckerFilterTest extends TestCase
{
    /**
     * @var CheckerFilter
     */
    private $checkerFilter;

    protected function setUp()
    {
        $this->checkerFilter = new CheckerFilter();
    }

    public function testFilterSniffs(): void
    {
        $sniffs = $this->checkerFilter->filterSniffs([
            FinalInterfaceSniff::class => [],
            DeclareStrictTypesFixer::class => []
        ]);

        $this->assertCount(1, $sniffs);
        $this->assertSame([FinalInterfaceSniff::class => []], $sniffs);
     }

    public function testFixers(): void
    {
         $fixers = $this->checkerFilter->filterFixers([
             FinalInterfaceSniff::class => [],
             DeclareStrictTypesFixer::class => []
         ]);

         $this->assertCount(1, $fixers);
         $this->assertSame([DeclareStrictTypesFixer::class => []], $fixers);
    }
}
