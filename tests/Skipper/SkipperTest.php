<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Skipper;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class SkipperTest extends TestCase
{
    /**
     * @var Skipper
     */
    private $skipper;

    protected function setUp(): void
    {
        $this->skipper = new Skipper($this->createSkipParameter(), []);
    }

    public function testNotSkipped(): void
    {
        $this->assertFalse($this->skipper->shouldSkipCheckerAndFile(
            FinalInterfaceFixer::class,
            new SmartFileInfo(__DIR__ . '/Source/someFile')
        ));

        $this->assertFalse($this->skipper->shouldSkipCheckerAndFile(
            FinalInterfaceFixer::class,
            new SmartFileInfo(__DIR__ . '/Source/someOtherFile')
        ));

        $this->assertFalse(
            $this->skipper->shouldSkipCodeAndFile('someSniff.someForeignCode', new SmartFileInfo(
                __DIR__ . '/Source/someFile'
            ))
        );
        $this->assertFalse(
            $this->skipper->shouldSkipCodeAndFile('someFixer.someOtherCode', new SmartFileInfo(
                __DIR__ . '/Source/someFile'
            ))
        );
    }

    public function testSkipMessage(): void
    {
        $container = (new ContainerFactory())->createWithConfigs([__DIR__ . '/skip.yml']);

        /** @var SniffFileProcessor $processor */
        $processor = $container->get(SniffFileProcessor::class);
        $processor->processFile(new SmartFileInfo(__DIR__ . '/skip.php.inc'));

        /** @var ErrorAndDiffCollector $errorCollector */
        $errorCollector = $container->get(ErrorAndDiffCollector::class);

        $this->assertCount(1, $errors = array_values($errorCollector->getErrors()));
        $this->assertCount(1, $errors[0]);

        $this->assertSame(11, $errors[0][0]->getLine());
        $this->assertSame(
            'Cognitive complexity for method "bar" is 2 but has to be less than or equal to 1.',
            $errors[0][0]->getMessage()
        );
    }

    public function testSkipped(): void
    {
        $this->assertTrue($this->skipper->shouldSkipCheckerAndFile(
            DeclareStrictTypesFixer::class,
            new SmartFileInfo(__DIR__ . '/Source/someFile')
        ));

        $this->assertTrue($this->skipper->shouldSkipCheckerAndFile(
            DeclareStrictTypesFixer::class,
            new SmartFileInfo(__DIR__ . '/Source/someDirectory/anotherFile.php')
        ));

        $this->assertTrue($this->skipper->shouldSkipCheckerAndFile(
            DeclareStrictTypesFixer::class,
            new SmartFileInfo(__DIR__ . '/Source/someDirectory/anotherFile.php')
        ));

        $this->assertTrue(
            $this->skipper->shouldSkipCodeAndFile(
                DeclareStrictTypesFixer::class . '.someCode',
                new SmartFileInfo(__DIR__ . '/Source/someFile')
            )
        );
        $this->assertTrue($this->skipper->shouldSkipCodeAndFile(
            DeclareStrictTypesFixer::class . '.someOtherCode',
            new SmartFileInfo(__DIR__ . '/Source/someDirectory/someFile')
        ));

        $this->assertTrue($this->skipper->shouldSkipCodeAndFile(
            DeclareStrictTypesFixer::class . '.someAnotherCode',
            new SmartFileInfo(__DIR__ . '/Source/someDirectory/someFile')
        ));

        $this->assertTrue($this->skipper->shouldSkipMessageAndFile(
            'some fishy code at line 5!',
            new SmartFileInfo(__DIR__ . '/Source/someFile')
        ));

        $this->assertTrue($this->skipper->shouldSkipMessageAndFile(
            'some another fishy code at line 5!',
            new SmartFileInfo(__DIR__ . '/Source/someDirectory/someFile.php')
        ));
    }

    /**
     * @return mixed[]
     */
    private function createSkipParameter(): array
    {
        return [
            DeclareStrictTypesFixer::class => ['Source/someFile', '*/someDirectory/*'],
            DeclareStrictTypesFixer::class . '.someCode' => null,
            DeclareStrictTypesFixer::class . '.someOtherCode' => ['*/someDirectory/*'],
            DeclareStrictTypesFixer::class . '.someAnotherCode' => ['someDirectory/*'],
            'some fishy code at line 5!' => null,
            'some another fishy code at line 5!' => ['someDirectory/*'],
        ];
    }
}
