<?php

declare (strict_types=1);
namespace ECSPrefix202606\Entropy\Console\ConsoleTable\ValueObject;

use ECSPrefix202606\Webmozart\Assert\Assert;
final class TableView
{
    /**
     * @readonly
     * @var string
     */
    private $title;
    /**
     * @readonly
     * @var string
     */
    private $label;
    /**
     * @var TableRow[]
     * @readonly
     */
    private $tableRows;
    /**
     * @readonly
     * @var bool
     */
    private $shouldIncludeRelative = \false;
    /**
     * @param TableRow[] $tableRows
     */
    public function __construct(string $title, string $label, array $tableRows, bool $shouldIncludeRelative = \false)
    {
        $this->title = $title;
        $this->label = $label;
        $this->tableRows = $tableRows;
        $this->shouldIncludeRelative = $shouldIncludeRelative;
        Assert::allIsInstanceOf($tableRows, TableRow::class);
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getLabel(): string
    {
        return $this->label;
    }
    public function isShouldIncludeRelative(): bool
    {
        return $this->shouldIncludeRelative;
    }
    /**
     * @return TableRow[]
     */
    public function getRows(): array
    {
        return $this->tableRows;
    }
}
