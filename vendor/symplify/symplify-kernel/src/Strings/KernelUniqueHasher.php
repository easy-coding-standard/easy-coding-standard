<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\SymplifyKernel\Strings;

use ConfigTransformer20210601\Nette\Utils\Strings;
use ConfigTransformer20210601\Symplify\SymplifyKernel\Exception\HttpKernel\TooGenericKernelClassException;
use ConfigTransformer20210601\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class KernelUniqueHasher
{
    /**
     * @var StringsConverter
     */
    private $stringsConverter;
    public function __construct()
    {
        $this->stringsConverter = new \ConfigTransformer20210601\Symplify\SymplifyKernel\Strings\StringsConverter();
    }
    public function hashKernelClass(string $kernelClass) : string
    {
        $this->ensureIsNotGenericKernelClass($kernelClass);
        $shortClassName = (string) \ConfigTransformer20210601\Nette\Utils\Strings::after($kernelClass, '\\', -1);
        $userSpecificShortClassName = $shortClassName . \get_current_user();
        return $this->stringsConverter->camelCaseToGlue($userSpecificShortClassName, '_');
    }
    /**
     * @return void
     */
    private function ensureIsNotGenericKernelClass(string $kernelClass)
    {
        if ($kernelClass !== \ConfigTransformer20210601\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel::class) {
            return;
        }
        $message = \sprintf('Instead of "%s", provide final Kernel class', \ConfigTransformer20210601\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel::class);
        throw new \ConfigTransformer20210601\Symplify\SymplifyKernel\Exception\HttpKernel\TooGenericKernelClassException($message);
    }
}
