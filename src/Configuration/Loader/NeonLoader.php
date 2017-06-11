<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Loader;

use Nette\Neon\Decoder;
use Nette\Utils\Strings;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;

final class NeonLoader implements LoaderInterface
{
    /**
     * @var LoaderResolverInterface
     */
    private $resolver;

    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function supports($resource, $type = null): bool
    {
        return Strings::endsWith($resource,'.neon');
    }

    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function load($resource, $type = null)
    {
        $neonFileContent = file_get_contents($resource);
        return (new Decoder)->decode($neonFileContent);
    }

    public function getResolver(): LoaderResolverInterface
    {
        return $this->resolver;
    }

    public function setResolver(LoaderResolverInterface $resolver): void
    {
        $this->resolver = $resolver;
    }
}