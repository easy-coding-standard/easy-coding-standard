<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symplify\PackageBuilder\Composer\VendorDirProvider;

/**
 * This service resolve parameters in import section, e.g:
 *
 * # config.yml
 * imports:
 *      - { resource: '%vendorDirectory%/symplify/easy-coding-standard/psr2.yml' }
 *
 * to their absolute path. That way you can load always from the same file independent on relative location.
 */
final class ParameterInImportResolver
{
    /**
     * @var string
     */
    private const IMPORTS_KEY = 'imports';

    /**
     * @var ParameterBag
     */
    private $decoratingParameterBag;

    public function __construct()
    {
        $this->decoratingParameterBag = new ParameterBag([
            'currentWorkingDirectory' => getcwd(),
            'vendorDirectory' => VendorDirProvider::provide(),
        ]);
    }

    /**
     * @param mixed[] $content
     * @return mixed[]
     */
    public function process(array $content): array
    {
        if (! isset($content[self::IMPORTS_KEY])) {
            return $content;
        }

        foreach ($content[self::IMPORTS_KEY] as $key => $import) {
            $import['resource'] = $this->decoratingParameterBag->resolveValue($import['resource']);
            $content[self::IMPORTS_KEY][$key] = $import;
        }

        return $content;
    }
}
