<?php

namespace Symplify\SymplifyKernel\ValueObject;

use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\KernelInterface;
use Symplify\PackageBuilder\Console\Input\StaticInputDetector;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\BootException;
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
    public function __construct($kernelClass, array $extraConfigs = [])
    {
        $kernelClass = (string) $kernelClass;
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
        } catch (Throwable $throwable) {
            $symfonyStyleFactory = new SymfonyStyleFactory();
            $symfonyStyle = $symfonyStyleFactory->create();
            $symfonyStyle->error($throwable->getMessage());
            exit(ShellCode::ERROR);
        }
    }

    /**
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    private function createKernel()
    {
        // random has is needed, so cache is invalidated and changes from config are loaded
        $environment = 'prod' . random_int(1, 100000);
        $kernelClass = $this->kernelClass;

        $kernel = new $kernelClass($environment, StaticInputDetector::isDebug());

        $this->setExtraConfigs($kernel, $kernelClass);

        return $kernel;
    }

    /**
     * @return void
     */
    private function booKernelAndRunApplication()
    {
        $kernel = $this->createKernel();
        if ($kernel instanceof ExtraConfigAwareKernelInterface && $this->extraConfigs !== []) {
            $kernel->setConfigs($this->extraConfigs);
        }

        $kernel->boot();

        $container = $kernel->getContainer();

        /** @var Application $application */
        $application = $container->get(Application::class);
        exit($application->run());
    }

    /**
     * @return void
     * @param string $kernelClass
     */
    private function setExtraConfigs(KernelInterface $kernel, $kernelClass)
    {
        $kernelClass = (string) $kernelClass;
        if ($this->extraConfigs === []) {
            return;
        }

        if (is_a($kernel, ExtraConfigAwareKernelInterface::class, true)) {
            /** @var ExtraConfigAwareKernelInterface $kernel */
            $kernel->setConfigs($this->extraConfigs);
        } else {
            $message = sprintf(
                'Extra configs are set, but the "%s" kernel class is missing "%s" interface',
                $kernelClass,
                ExtraConfigAwareKernelInterface::class
            );
            throw new BootException($message);
        }
    }

    /**
     * @param class-string $kernelClass
     * @return void
     */
    private function setKernelClass($kernelClass)
    {
        $kernelClass = (string) $kernelClass;
        if (! is_a($kernelClass, KernelInterface::class, true)) {
            $message = sprintf('Class "%s" must by type of "%s"', $kernelClass, KernelInterface::class);
            throw new BootException($message);
        }

        $this->kernelClass = $kernelClass;
    }
}
