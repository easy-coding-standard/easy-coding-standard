<?php

declare (strict_types=1);
namespace ECSPrefix20210825\Symplify\SymplifyKernel\Strings;

use ECSPrefix20210825\Nette\Utils\Strings;
use ECSPrefix20210825\Symplify\SymplifyKernel\Exception\HttpKernel\TooGenericKernelClassException;
use ECSPrefix20210825\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class KernelUniqueHasher
{
    /**
     * @var \Symplify\SymplifyKernel\Strings\StringsConverter
     */
    private $stringsConverter;
    public function __construct()
    {
        $this->stringsConverter = new \ECSPrefix20210825\Symplify\SymplifyKernel\Strings\StringsConverter();
    }
    public function hashKernelClass(string $kernelClass) : string
    {
        $this->ensureIsNotGenericKernelClass($kernelClass);
        $shortClassName = (string) \ECSPrefix20210825\Nette\Utils\Strings::after($kernelClass, '\\', -1);
        $userSpecificShortClassName = $shortClassName . \get_current_user();
        return $this->stringsConverter->camelCaseToGlue($userSpecificShortClassName, '_');
    }
    private function ensureIsNotGenericKernelClass(string $kernelClass) : void
    {
        if ($kernelClass !== \ECSPrefix20210825\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel::class) {
            return;
        }
        $message = \sprintf('Instead of "%s", provide final Kernel class', \ECSPrefix20210825\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel::class);
        throw new \ECSPrefix20210825\Symplify\SymplifyKernel\Exception\HttpKernel\TooGenericKernelClassException($message);
    }
}
