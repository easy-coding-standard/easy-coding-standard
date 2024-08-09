<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Configuration\EditorConfig;

use Exception;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Symplify\EasyCodingStandard\Configuration\EditorConfig\EditorConfig;
use Symplify\EasyCodingStandard\Configuration\EditorConfig\EditorConfigFactory;
use Symplify\EasyCodingStandard\Configuration\EditorConfig\EndOfLine;
use Symplify\EasyCodingStandard\Configuration\EditorConfig\IndentStyle;
use Symplify\EasyCodingStandard\Configuration\EditorConfig\QuoteType;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractTestCase;

final class EditorConfigFactoryTest extends AbstractTestCase
{
    public function testLoadingFromProjectRoot(): void
    {
        // Depends on the ECS project itself using an `.editorconfig`.
        $editorConfigFactory = new EditorConfigFactory();
        $editorConfig = $editorConfigFactory->load();

        $this->assertEquals($editorConfig, new EditorConfig(
            indentStyle: IndentStyle::Space,
            endOfLine: EndOfLine::Posix,
            insertFinalNewline: true,
            trimTrailingWhitespace: true,
            maxLineLength: null,
            quoteType: null
        ));
    }

    #[RunInSeparateProcess]
    public function testLoadingBadFilepath(): void
    {
        chdir(__DIR__);

        $this->expectException(Exception::class);

        $editorConfigFactory = new EditorConfigFactory();
        $editorConfigFactory->load();
    }

    public function testParsingInvalidIniFile(): void
    {
        $this->expectException(Exception::class);

        $editorConfigFactory = new EditorConfigFactory();
        @$editorConfigFactory->parse(<<<INI
            fleeb!
            INI
        );
    }

    public function testLoadsExpectedSections(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(
                <<<INI
                [*]
                indent_style = space
                max_line_length = 100

                [*.php]
                indent_style = tab
                end_of_line = lf
                INI
            ),
            new EditorConfig(
                indentStyle: IndentStyle::Tab,
                endOfLine: EndOfLine::Posix,
                insertFinalNewline: null,
                trimTrailingWhitespace: null,
                maxLineLength: 100,
                quoteType: null
            ),
        );
    }

    public function testEmpty(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(''),
            new EditorConfig(
                indentStyle: null,
                endOfLine: null,
                insertFinalNewline: null,
                trimTrailingWhitespace: null,
                maxLineLength: null,
                quoteType: null
            )
        );
    }

    public function testIndentStyleSpaces(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(
                <<<INI
                [*]
                indent_style = space
                INI
            ),
            new EditorConfig(
                indentStyle: IndentStyle::Space,
                endOfLine: null,
                insertFinalNewline: null,
                trimTrailingWhitespace: null,
                maxLineLength: null,
                quoteType: null
            )
        );
    }

    public function testIndentStyleTabs(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(
                <<<INI
                [*]
                indent_style = tab
                INI
            ),
            new EditorConfig(
                indentStyle: IndentStyle::Tab,
                endOfLine: null,
                insertFinalNewline: null,
                trimTrailingWhitespace: null,
                maxLineLength: null,
                quoteType: null
            )
        );
    }

    public function testEndOfLinePosix(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(
                <<<INI
                [*]
                end_of_line = lf
                INI
            ),
            new EditorConfig(
                indentStyle: null,
                endOfLine: EndOfLine::Posix,
                insertFinalNewline: null,
                trimTrailingWhitespace: null,
                maxLineLength: null,
                quoteType: null
            )
        );
    }

    public function testEndOfLineLegacy(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(
                <<<INI
                [*]
                end_of_line = cr
                INI
            ),
            new EditorConfig(
                indentStyle: null,
                endOfLine: EndOfLine::Legacy,
                insertFinalNewline: null,
                trimTrailingWhitespace: null,
                maxLineLength: null,
                quoteType: null
            )
        );
    }

    public function testEndOfLineWindows(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(
                <<<INI
                [*]
                end_of_line = crlf
                INI
            ),
            new EditorConfig(
                indentStyle: null,
                endOfLine: EndOfLine::Windows,
                insertFinalNewline: null,
                trimTrailingWhitespace: null,
                maxLineLength: null,
                quoteType: null
            )
        );
    }

    public function testTrimTrailingWhitespaceEnabled(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(
                <<<INI
                [*]
                trim_trailing_whitespace = true
                INI
            ),
            new EditorConfig(
                indentStyle: null,
                endOfLine: null,
                insertFinalNewline: null,
                trimTrailingWhitespace: true,
                maxLineLength: null,
                quoteType: null
            )
        );
    }

    public function testTrimTrailingWhitespaceDisabled(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(
                <<<INI
                [*]
                trim_trailing_whitespace = false
                INI
            ),
            new EditorConfig(
                indentStyle: null,
                endOfLine: null,
                insertFinalNewline: null,
                trimTrailingWhitespace: false,
                maxLineLength: null,
                quoteType: null
            )
        );
    }

    public function testInsertFinalNewlineEnabled(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(
                <<<INI
                [*]
                insert_final_newline = true
                INI
            ),
            new EditorConfig(
                indentStyle: null,
                endOfLine: null,
                insertFinalNewline: true,
                trimTrailingWhitespace: null,
                maxLineLength: null,
                quoteType: null
            )
        );
    }

    public function testInsertFinalNewlineDisabled(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(
                <<<INI
                [*]
                insert_final_newline = false
                INI
            ),
            new EditorConfig(
                indentStyle: null,
                endOfLine: null,
                insertFinalNewline: false,
                trimTrailingWhitespace: null,
                maxLineLength: null,
                quoteType: null
            )
        );
    }

    public function testMaxLineLength(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(
                <<<INI
                [*]
                max_line_length = 63
                INI
            ),
            new EditorConfig(
                indentStyle: null,
                endOfLine: null,
                insertFinalNewline: null,
                trimTrailingWhitespace: null,
                maxLineLength: 63,
                quoteType: null
            )
        );
    }

    public function quoteTypeAuto(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(
                <<<INI
                [*]
                quote_type = auto
                INI
            ),
            new EditorConfig(
                indentStyle: null,
                endOfLine: null,
                insertFinalNewline: null,
                trimTrailingWhitespace: null,
                maxLineLength: null,
                quoteType: QuoteType::Auto
            )
        );
    }

    public function quoteTypeSingle(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(
                <<<INI
                [*]
                quote_type = single
                INI
            ),
            new EditorConfig(
                indentStyle: null,
                endOfLine: null,
                insertFinalNewline: null,
                trimTrailingWhitespace: null,
                maxLineLength: null,
                quoteType: QuoteType::Single
            )
        );
    }

    public function quoteTypeDouble(): void
    {
        $this->assertEquals(
            (new EditorConfigFactory())->parse(
                <<<INI
                [*]
                quote_type = double
                INI
            ),
            new EditorConfig(
                indentStyle: null,
                endOfLine: null,
                insertFinalNewline: null,
                trimTrailingWhitespace: null,
                maxLineLength: null,
                quoteType: QuoteType::Double
            )
        );
    }
}
