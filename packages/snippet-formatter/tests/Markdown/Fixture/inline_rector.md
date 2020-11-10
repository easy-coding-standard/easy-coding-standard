Move value object to ValueObject namespace/directory

```php
declare(strict_types=1);

use Rector\Autodiscovery\Rector\FileSystem\MoveValueObjectsToValueObjectDirectoryRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(MoveValueObjectsToValueObjectDirectoryRector::class)
        ->call('configure', [[MoveValueObjectsToValueObjectDirectoryRector::TYPES => ['ValueObjectInterfaceClassName'], MoveValueObjectsToValueObjectDirectoryRector::SUFFIXES => ['Search'], MoveValueObjectsToValueObjectDirectoryRector::ENABLE_VALUE_OBJECT_GUESSING => true]]);
};
```
-----
Move value object to ValueObject namespace/directory

```php
use Rector\Autodiscovery\Rector\FileSystem\MoveValueObjectsToValueObjectDirectoryRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(MoveValueObjectsToValueObjectDirectoryRector::class)
        ->call('configure', [[
MoveValueObjectsToValueObjectDirectoryRector::TYPES => ['ValueObjectInterfaceClassName'], MoveValueObjectsToValueObjectDirectoryRector::SUFFIXES => ['Search'], MoveValueObjectsToValueObjectDirectoryRector::ENABLE_VALUE_OBJECT_GUESSING => true
]]);
};
```
