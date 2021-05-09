<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver\Tests\Bootstrap;

use PHPUnit\Framework\TestCase;
use Symplify\SetConfigResolver\Bootstrap\InvalidSetReporter;
use Symplify\SetConfigResolver\Exception\SetNotFoundException;

final class InvalidSetReporterTest extends TestCase
{
    /**
     * @var InvalidSetReporter
     */
    private $invalidSetReporter;

    protected function setUp(): void
    {
        $this->invalidSetReporter = new InvalidSetReporter();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test(): void
    {
        $setNotFoundException = new SetNotFoundException('not found', 'one', ['two', 'three']);
        $this->invalidSetReporter->report($setNotFoundException);
    }
}
