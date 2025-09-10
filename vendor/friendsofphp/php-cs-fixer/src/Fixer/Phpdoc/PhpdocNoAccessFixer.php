<?php

declare (strict_types=1);
/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PhpdocNoAccessFixer extends AbstractProxyFixer
{
    public function getDefinition() : FixerDefinitionInterface
    {
        return new FixerDefinition('`@access` annotations must be removed from PHPDoc.', [new CodeSample(<<<'PHP'
<?php

namespace ECSPrefix202509;

class Foo
{
    /**
    * @internal
    * @access private
    */
    private $bar;
}
\class_alias('ECSPrefix202509\\Foo', 'Foo', \false);

PHP
)]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoEmptyPhpdocFixer, PhpdocAlignFixer, PhpdocSeparationFixer, PhpdocTrimFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority() : int
    {
        return parent::getPriority();
    }
    protected function createProxyFixers() : array
    {
        $fixer = new \PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer();
        $fixer->configure(['annotations' => ['access'], 'case_sensitive' => \true]);
        return [$fixer];
    }
}
