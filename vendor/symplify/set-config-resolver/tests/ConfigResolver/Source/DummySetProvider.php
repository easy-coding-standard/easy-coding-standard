<?php

declare (strict_types=1);
namespace Symplify\SetConfigResolver\Tests\ConfigResolver\Source;

use Symplify\SetConfigResolver\Contract\SetProviderInterface;
use Symplify\SetConfigResolver\Provider\AbstractSetProvider;
use Symplify\SetConfigResolver\ValueObject\Set;
use Symplify\SmartFileSystem\SmartFileInfo;
final class DummySetProvider extends \Symplify\SetConfigResolver\Provider\AbstractSetProvider implements \Symplify\SetConfigResolver\Contract\SetProviderInterface
{
    /**
     * @var Set[]
     */
    private $sets = [];
    public function __construct()
    {
        $this->sets[] = new \Symplify\SetConfigResolver\ValueObject\Set('some_php_set', new \Symplify\SmartFileSystem\SmartFileInfo(__DIR__ . '/../Source/some_php_set.php'));
    }
    /**
     * @return Set[]
     */
    public function provide() : array
    {
        return $this->sets;
    }
}
