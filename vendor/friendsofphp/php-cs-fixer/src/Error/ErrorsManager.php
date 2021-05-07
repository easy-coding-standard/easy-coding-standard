<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Error;

/**
 * Manager of errors that occur during fixing.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ErrorsManager
{
    /**
     * @var Error[]
     */
    private $errors = [];
    /**
     * Returns errors reported during linting before fixing.
     *
     * @return mixed[]
     */
    public function getInvalidErrors()
    {
        return \array_filter($this->errors, static function (\PhpCsFixer\Error\Error $error) {
            return \PhpCsFixer\Error\Error::TYPE_INVALID === $error->getType();
        });
    }
    /**
     * Returns errors reported during fixing.
     *
     * @return mixed[]
     */
    public function getExceptionErrors()
    {
        return \array_filter($this->errors, static function (\PhpCsFixer\Error\Error $error) {
            return \PhpCsFixer\Error\Error::TYPE_EXCEPTION === $error->getType();
        });
    }
    /**
     * Returns errors reported during linting after fixing.
     *
     * @return mixed[]
     */
    public function getLintErrors()
    {
        return \array_filter($this->errors, static function (\PhpCsFixer\Error\Error $error) {
            return \PhpCsFixer\Error\Error::TYPE_LINT === $error->getType();
        });
    }
    /**
     * Returns true if no errors were reported.
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->errors);
    }
    /**
     * @return void
     * @param \PhpCsFixer\Error\Error $error
     */
    public function report($error)
    {
        $this->errors[] = $error;
    }
}
