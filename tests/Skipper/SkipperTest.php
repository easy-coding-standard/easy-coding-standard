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
            __DIR__ . '/someFile'
        ));

        $this->assertFalse($this->skipper->shouldSkipCheckerAndFile(
            FinalInterfaceFixer::class,
            __DIR__ . '/someOtherFile'
        ));

        $this->assertFalse($this->skipper->shouldSkipCodeAndFile('someSniff.someForeignCode', __DIR__ . 'someFile'));
        $this->assertFalse($this->skipper->shouldSkipCodeAndFile('someFixer.someOtherCode', __DIR__ . 'someFile'));
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
            __DIR__ . '/someFile'
        ));

        $this->assertTrue($this->skipper->shouldSkipCheckerAndFile(
            DeclareStrictTypesFixer::class,
            __DIR__ . '/someDirectory/anotherFile.php'
        ));

        $this->assertTrue($this->skipper->shouldSkipCheckerAndFile(
            DeclareStrictTypesFixer::class,
            __DIR__ . '/someDirectory/anotherFile.php'
        ));

        $this->assertTrue(
            $this->skipper->shouldSkipCodeAndFile(DeclareStrictTypesFixer::class . '.someCode', __DIR__ . '/someFile')
        );
        $this->assertTrue($this->skipper->shouldSkipCodeAndFile(
            DeclareStrictTypesFixer::class . '.someOtherCode',
            __DIR__ . '/someDirectory/someFile'
        ));

        $this->assertTrue($this->skipper->shouldSkipCodeAndFile(
            DeclareStrictTypesFixer::class . '.someAnotherCode',
            __DIR__ . '/someDirectory/someFile'
        ));

        $this->assertTrue($this->skipper->shouldSkipMessageAndFile(
            'some fishy code at line 5!',
            __DIR__ . '/someFile'
        ));

        $this->assertTrue($this->skipper->shouldSkipMessageAndFile(
            'some another fishy code at line 5!',
            __DIR__ . '/someDirectory/someFile.php'
        ));
    }

    /**
     * @return mixed[]
     */
    private function createSkipParameter(): array
    {
        return [
            DeclareStrictTypesFixer::class => ['someFile', '*/someDirectory/*'],
            DeclareStrictTypesFixer::class . '.someCode' => null,
            DeclareStrictTypesFixer::class . '.someOtherCode' => ['*/someDirectory/*'],
            DeclareStrictTypesFixer::class . '.someAnotherCode' => ['someDirectory/*'],
            'some fishy code at line 5!' => null,
            'some another fishy code at line 5!' => ['someDirectory/*'],
        ];
    }
}
