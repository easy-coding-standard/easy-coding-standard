<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\EditorConfig;

use Exception;

class EditorConfigFactory
{
    public function load(): EditorConfig
    {
        // By default, composer executes scripts from within the project root.
        $projectRoot = getcwd();
        $editorConfigPath = $projectRoot . '/.editorconfig';

        if (! file_exists($editorConfigPath)) {
            throw new Exception('No .editorconfig found.');
        }

        $configFileContent = file_get_contents($editorConfigPath);

        if ($configFileContent === false) {
            throw new Exception('Unable to load .editorconfig.');
        }

        return $this->parse($configFileContent);
    }

    public function parse(string $editorConfigFileContents): EditorConfig
    {
        $fullConfig = parse_ini_string($editorConfigFileContents, true, INI_SCANNER_TYPED);

        if ($fullConfig === false) {
            throw new Exception('Unable to parse .editorconfig.');
        }

        $config = [...$fullConfig['*'] ?? [], ...$fullConfig['*.php'] ?? []];

        // Just letting "validation" happen with PHP's type hints.
        return new EditorConfig(
            indentStyle: $config['indent_style'] ?? null,
            endOfLine: $config['end_of_line'] ?? null,
            trimTrailingWhitespace: $this->field($config, 'trim_trailing_whitespace', $this->id(...)),
            insertFinalNewline: $this->field($config, 'insert_final_newline', $this->id(...)),
            maxLineLength: $this->field($config, 'max_line_length', $this->id(...)),
            quoteType: $config['quote_type'] ?? null,
        );
    }

    /**
     * @template From
     * @template To
     * @param mixed[] $config
     * @param callable(From): To $transform
     * @return To|null
     */
    private function field(array $config, string $field, callable $transform): mixed
    {
        if (! isset($config[$field])) {
            return null;
        }

        return $transform($config[$field]);
    }

    /**
     * @template T
     * @param T $value
     * @return T
     */
    private function id(mixed $value): mixed
    {
        return $value;
    }
}
