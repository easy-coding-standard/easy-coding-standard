<?php

declare (strict_types=1);
namespace ECSPrefix20211101\Symplify\SymplifyKernel\ValueObject;

use ECSPrefix20211101\Symfony\Component\Console\Application;
use ECSPrefix20211101\Symfony\Component\Console\Command\Command;
use ECSPrefix20211101\Symfony\Component\HttpKernel\KernelInterface;
use ECSPrefix20211101\Symplify\PackageBuilder\Console\Input\StaticInputDetector;
use ECSPrefix20211101\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use ECSPrefix20211101\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use ECSPrefix20211101\Symplify\SymplifyKernel\Contract\LightKernelInterface;
use ECSPrefix20211101\Symplify\SymplifyKernel\Exception\BootException;
use Throwable;
/**
 * @api
 */
final class KernelBootAndApplicationRun
{
    /**
     * @var class-string<\Symfony\Component\HttpKernel\KernelInterface|\Symplify\SymplifyKernel\Contract\LightKernelInterface>
     */
    private $kernelClass;
    /**
     * @var string[]
     */
    private $extraConfigs = [];
    /**
     * @param class-string<KernelInterface|LightKernelInterface> $kernelClass
     * @param string[] $extraConfigs
     */
    public function __construct(string $kernelClass, array $extraConfigs = [])
    {
        $this->kernelClass = $kernelClass;
        $this->extraConfigs = $extraConfigs;
        $this->validateKernelClass($this->kernelClass);
    }
    public function run() : void
    {
        try {
            $this->booKernelAndRunApplication();
        } catch (\Throwable $throwable) {
            $symfonyStyleFactory = new \ECSPrefix20211101\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory();
            $symfonyStyle = $symfonyStyleFactory->create();
            $symfonyStyle->error($throwable->getMessage());
            exit(\ECSPrefix20211101\Symfony\Component\Console\Command\Command::FAILURE);
        }
    }
    /**
     * @return \Symfony\Component\HttpKernel\KernelInterface|\Symplify\SymplifyKernel\Contract\LightKernelInterface
     */
    private function createKernel()
    {
        // random has is needed, so cache is invalidated and changes from config are loaded
        $kernelClass = $this->kernelClass;
        if (\is_a($kernelClass, \ECSPrefix20211101\Symplify\SymplifyKernel\Contract\LightKernelInterface::class, \true)) {
            return new $kernelClass();
        }
        $environment = 'prod' . \random_int(1, 100000);
        $kernel = new $kernelClass($environment, \ECSPrefix20211101\Symplify\PackageBuilder\Console\Input\StaticInputDetector::isDebug());
        $this->setExtraConfigs($kernel, $kernelClass);
        return $kernel;
    }
    private function booKernelAndRunApplication() : void
    {
        $kernel = $this->createKernel();
        if ($kernel instanceof \ECSPrefix20211101\Symplify\SymplifyKernel\Contract\LightKernelInterface) {
            $container = $kernel->createFromConfigs($this->extraConfigs);
        } else {
            if ($kernel instanceof \ECSPrefix20211101\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface && $this->extraConfigs !== []) {
                $kernel->setConfigs($this->extraConfigs);
            }
            $kernel->boot();
            $container = $kernel->getContainer();
        }
        /** @var Application $application */
        $application = $container->get(\ECSPrefix20211101\Symfony\Component\Console\Application::class);
        exit($application->run());
    }
    private function setExtraConfigs(\ECSPrefix20211101\Symfony\Component\HttpKernel\KernelInterface $kernel, string $kernelClass) : void
    {
        if ($this->extraConfigs === []) {
            return;
        }
        if (\is_a($kernel, \ECSPrefix20211101\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface::class, \true)) {
            /** @var ExtraConfigAwareKernelInterface $kernel */
            $kernel->setConfigs($this->extraConfigs);
        } else {
            $message = \sprintf('Extra configs are set, but the "%s" kernel class is missing "%s" interface', $kernelClass, \ECSPrefix20211101\Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface::class);
            throw new \ECSPrefix20211101\Symplify\SymplifyKernel\Exception\BootException($message);
        }
    }
    /**
     * @param class-string $kernelClass
     */
    private function validateKernelClass(string $kernelClass) : void
    {
        if (\is_a($kernelClass, \ECSPrefix20211101\Symfony\Component\HttpKernel\KernelInterface::class, \true)) {
            return;
        }
        if (\is_a($kernelClass, \ECSPrefix20211101\Symplify\SymplifyKernel\Contract\LightKernelInterface::class, \true)) {
            return;
        }
        $errorMessage = \sprintf('Class "%s" must by type of "%s" or "%s"', $kernelClass, \ECSPrefix20211101\Symfony\Component\HttpKernel\KernelInterface::class, \ECSPrefix20211101\Symplify\SymplifyKernel\Contract\LightKernelInterface::class);
        throw new \ECSPrefix20211101\Symplify\SymplifyKernel\Exception\BootException($errorMessage);
    }
}
