<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

final class ErrorFilter
{
    /**
     * @var string[]
     */
    private $ignoredErrors = [];

    /**
     * @param string[] $ignoredErrors
     */
    public function setIgnoredErrors(array $ignoredErrors)
    {
        $this->ignoredErrors = $ignoredErrors;
    }

//    @todo
//    public function filterOutFixableErrors()
//    {
//
//    }

    public function filterOutIgnoredErrors()
    {
        $errors = array_values(array_filter($errors, function (string $error) use (&$unmatchedIgnoredErrors): bool {
            foreach ($this->ignoreErrors as $i => $ignore) {
                if (\Nette\Utils\Strings::match($error, $ignore) !== null) {
                    unset($unmatchedIgnoredErrors[$i]);
                    return false;
                }
            }
            return true;
        }));
    }
}
