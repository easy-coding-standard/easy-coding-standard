<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Loader;

use Nette\Neon\Decoder;
use Nette\Utils\Strings;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class NeonLoader implements LoaderInterface
{
    /**
     * @var LoaderResolverInterface
     */
    private $resolver;
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
    }

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
    public function load($resource, $type = null): void
    {
        $neonFileContent = file_get_contents($resource);
        $content = (new Decoder)->decode($neonFileContent);
        foreach ($content as $key => $value) {
            $this->containerBuilder->setParameter($key, $value);
        }
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