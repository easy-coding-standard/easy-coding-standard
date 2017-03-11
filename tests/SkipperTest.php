<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Sniffs\Classes\ClassDeclarationSniff;
use Symplify\EasyCodingStandard\Configuration\ConfigurationNormalizer;
use Symplify\EasyCodingStandard\Skipper;

final class SkipperTest extends TestCase
{
    /**
     * @var Skipper
     */
    private $skipper;

    protected function setUp()
    {
        $this->skipper = new Skipper(new ConfigurationNormalizer());
    }

    public function test()
    {
        $this->skipper->setSkipped([
            'someFile' => [
                DeclareStrictTypesFixer::class
            ]
        ]);

        $this->assertFalse($this->skipper->shouldSkipCheckerAndFile(
            ClassDeclarationSniff::class, 'someFile'
        ));

        $this->assertFalse($this->skipper->shouldSkipCheckerAndFile(
            DeclareStrictTypesFixer::class, 'someOtherFile'
        ));

        $this->assertTrue($this->skipper->shouldSkipCheckerAndFile(
            DeclareStrictTypesFixer::class, 'someFile'
        ));
    }
}