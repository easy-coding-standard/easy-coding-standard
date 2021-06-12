<?php

declare (strict_types=1);
namespace ECSPrefix20210612\Symplify\SymplifyKernel\ValueObject;

use ECSPrefix20210612\Symfony\Component\Console\Application;
use ECSPrefix20210612\Symfony\Component\HttpKernel\KernelInterface;
use ECSPrefix20210612\Symplify\PackageBuilder\Console\Input\StaticInputDetector;
use ECSPrefix20210612\Symplify\PackageBuilder\Console\ShellCode;
use ECSPrefix20210612\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use ECSPrefix20210612\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use ECSPrefix20210612\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20210612\Symplify\SymplifyKernel\Exception\BootException;
use Throwable;
final class KernelBootAndApplicationRun
{
    /**
     * @var class-string
     */
    private $kernelClass;
    /**
     * @var string[]|SmartFileInfo[]
     */
    private $extraConfigs = [];
    /**
     * @param class-string $kernelClass
     * @param string[]|SmartFileInfo[] $extraConfigs
     */
    public function __construct(string $kernelClass, array $extraConfigs = [])
    {
        $this->setKernelClass($kernelClass);
        $this->extraConfigs = $extraConfigs;
    }
    /**
     * @return void
     */
    public function run()
    {
        try {
            $this->booKernelAndRunApplication();
        } catch (\Throwable $throwable) {
            $symfonyStyleFactory = new \ECSPrefix20210612\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory();
            $symfonyStyle = $symfonyStyleFactory->create();
            $symfonyStyle->error($throwable->getMessage());
            exit(\ECSPrefix20210612\Symplify\PackageBuilder\Console\ShellCode::ERROR);
        }
    }
    private function createKernel() : \ECSPrefix20210612\Symfony\Component\HttpKernel\KernelInterface
    {
        // random has is needed, so cache is invalidated and changes from config are loaded
        $environment = 'prod' . \random_int(1, 100000);
        $kernelClass = $this->kernelClass;
        $kernel = new $kernelClass($environment, \ECSPrefix20210612\Symplify\PackageBuilder\Console\Input\StaticInputDetector::isDebug());
        $this->setExtraConfigs($kernel, $kernelClass);
        return $kernel;
    }
    /**
     * @return void
     */
    private function booKernelAndRunApplication()
    {
        $kernel = $this->createKernel();
        if ($kernel instanceof \ECSPrefix20210612\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface && $this->extraConfigs !== []) {
            $kernel->setConfigs($this->extraConfigs);
        }
        $kernel->boot();
        $container = $kernel->getContainer();
        /** @var Application $application */
        $application = $container->get(\ECSPrefix20210612\Symfony\Component\Console\Application::class);
        exit($application->run());
    }
    /**
     * @return void
     */
    private function setExtraConfigs(\ECSPrefix20210612\Symfony\Component\HttpKernel\KernelInterface $kernel, string $kernelClass)
    {
        if ($this->extraConfigs === []) {
            return;
        }
        if (\is_a($kernel, \ECSPrefix20210612\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface::class, \true)) {
            /** @var ExtraConfigAwareKernelInterface $kernel */
            $kernel->setConfigs($this->extraConfigs);
        } else {
            $message = \sprintf('Extra configs are set, but the "%s" kernel class is missing "%s" interface', $kernelClass, \ECSPrefix20210612\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface::class);
            throw new \ECSPrefix20210612\Symplify\SymplifyKernel\Exception\BootException($message);
        }
    }
    /**
     * @param class-string $kernelClass
     * @return void
     */
    private function setKernelClass(string $kernelClass)
    {
        if (!\is_a($kernelClass, \ECSPrefix20210612\Symfony\Component\HttpKernel\KernelInterface::class, \true)) {
            $message = \sprintf('Class "%s" must by type of "%s"', $kernelClass, \ECSPrefix20210612\Symfony\Component\HttpKernel\KernelInterface::class);
            throw new \ECSPrefix20210612\Symplify\SymplifyKernel\Exception\BootException($message);
        }
        $this->kernelClass = $kernelClass;
    }
}
