<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser;

use ConfigTransformer20210601\PhpParser\Node\Expr\Include_;
use ConfigTransformer20210601\PhpParser\Node\Stmt\Class_;
use ConfigTransformer20210601\PhpParser\Node\Stmt\GroupUse;
use ConfigTransformer20210601\PhpParser\Node\Stmt\Use_;
use ConfigTransformer20210601\PhpParser\Node\Stmt\UseUse;
class NodeDumper
{
    private $dumpComments;
    private $dumpPositions;
    private $code;
    /**
     * Constructs a NodeDumper.
     *
     * Supported options:
     *  * bool dumpComments: Whether comments should be dumped.
     *  * bool dumpPositions: Whether line/offset information should be dumped. To dump offset
     *                        information, the code needs to be passed to dump().
     *
     * @param array $options Options (see description)
     */
    public function __construct(array $options = [])
    {
        $this->dumpComments = !empty($options['dumpComments']);
        $this->dumpPositions = !empty($options['dumpPositions']);
    }
    /**
     * Dumps a node or array.
     *
     * @param array|Node  $node Node or array to dump
     * @param string|null $code Code corresponding to dumped AST. This only needs to be passed if
     *                          the dumpPositions option is enabled and the dumping of node offsets
     *                          is desired.
     *
     * @return string Dumped value
     */
    public function dump($node, string $code = null) : string
    {
        $this->code = $code;
        return $this->dumpRecursive($node);
    }
    protected function dumpRecursive($node)
    {
        if ($node instanceof \ConfigTransformer20210601\PhpParser\Node) {
            $r = $node->getType();
            if ($this->dumpPositions && null !== ($p = $this->dumpPosition($node))) {
                $r .= $p;
            }
            $r .= '(';
            foreach ($node->getSubNodeNames() as $key) {
                $r .= "\n    " . $key . ': ';
                $value = $node->{$key};
                if (null === $value) {
                    $r .= 'null';
                } elseif (\false === $value) {
                    $r .= 'false';
                } elseif (\true === $value) {
                    $r .= 'true';
                } elseif (\is_scalar($value)) {
                    if ('flags' === $key || 'newModifier' === $key) {
                        $r .= $this->dumpFlags($value);
                    } elseif ('type' === $key && $node instanceof \ConfigTransformer20210601\PhpParser\Node\Expr\Include_) {
                        $r .= $this->dumpIncludeType($value);
                    } elseif ('type' === $key && ($node instanceof \ConfigTransformer20210601\PhpParser\Node\Stmt\Use_ || $node instanceof \ConfigTransformer20210601\PhpParser\Node\Stmt\UseUse || $node instanceof \ConfigTransformer20210601\PhpParser\Node\Stmt\GroupUse)) {
                        $r .= $this->dumpUseType($value);
                    } else {
                        $r .= $value;
                    }
                } else {
                    $r .= \str_replace("\n", "\n    ", $this->dumpRecursive($value));
                }
            }
            if ($this->dumpComments && ($comments = $node->getComments())) {
                $r .= "\n    comments: " . \str_replace("\n", "\n    ", $this->dumpRecursive($comments));
            }
        } elseif (\is_array($node)) {
            $r = 'array(';
            foreach ($node as $key => $value) {
                $r .= "\n    " . $key . ': ';
                if (null === $value) {
                    $r .= 'null';
                } elseif (\false === $value) {
                    $r .= 'false';
                } elseif (\true === $value) {
                    $r .= 'true';
                } elseif (\is_scalar($value)) {
                    $r .= $value;
                } else {
                    $r .= \str_replace("\n", "\n    ", $this->dumpRecursive($value));
                }
            }
        } elseif ($node instanceof \ConfigTransformer20210601\PhpParser\Comment) {
            return $node->getReformattedText();
        } else {
            throw new \InvalidArgumentException('Can only dump nodes and arrays.');
        }
        return $r . "\n)";
    }
    protected function dumpFlags($flags)
    {
        $strs = [];
        if ($flags & \ConfigTransformer20210601\PhpParser\Node\Stmt\Class_::MODIFIER_PUBLIC) {
            $strs[] = 'MODIFIER_PUBLIC';
        }
        if ($flags & \ConfigTransformer20210601\PhpParser\Node\Stmt\Class_::MODIFIER_PROTECTED) {
            $strs[] = 'MODIFIER_PROTECTED';
        }
        if ($flags & \ConfigTransformer20210601\PhpParser\Node\Stmt\Class_::MODIFIER_PRIVATE) {
            $strs[] = 'MODIFIER_PRIVATE';
        }
        if ($flags & \ConfigTransformer20210601\PhpParser\Node\Stmt\Class_::MODIFIER_ABSTRACT) {
            $strs[] = 'MODIFIER_ABSTRACT';
        }
        if ($flags & \ConfigTransformer20210601\PhpParser\Node\Stmt\Class_::MODIFIER_STATIC) {
            $strs[] = 'MODIFIER_STATIC';
        }
        if ($flags & \ConfigTransformer20210601\PhpParser\Node\Stmt\Class_::MODIFIER_FINAL) {
            $strs[] = 'MODIFIER_FINAL';
        }
        if ($strs) {
            return \implode(' | ', $strs) . ' (' . $flags . ')';
        } else {
            return $flags;
        }
    }
    protected function dumpIncludeType($type)
    {
        $map = [\ConfigTransformer20210601\PhpParser\Node\Expr\Include_::TYPE_INCLUDE => 'TYPE_INCLUDE', \ConfigTransformer20210601\PhpParser\Node\Expr\Include_::TYPE_INCLUDE_ONCE => 'TYPE_INCLUDE_ONCE', \ConfigTransformer20210601\PhpParser\Node\Expr\Include_::TYPE_REQUIRE => 'TYPE_REQUIRE', \ConfigTransformer20210601\PhpParser\Node\Expr\Include_::TYPE_REQUIRE_ONCE => 'TYPE_REQUIRE_ONCE'];
        if (!isset($map[$type])) {
            return $type;
        }
        return $map[$type] . ' (' . $type . ')';
    }
    protected function dumpUseType($type)
    {
        $map = [\ConfigTransformer20210601\PhpParser\Node\Stmt\Use_::TYPE_UNKNOWN => 'TYPE_UNKNOWN', \ConfigTransformer20210601\PhpParser\Node\Stmt\Use_::TYPE_NORMAL => 'TYPE_NORMAL', \ConfigTransformer20210601\PhpParser\Node\Stmt\Use_::TYPE_FUNCTION => 'TYPE_FUNCTION', \ConfigTransformer20210601\PhpParser\Node\Stmt\Use_::TYPE_CONSTANT => 'TYPE_CONSTANT'];
        if (!isset($map[$type])) {
            return $type;
        }
        return $map[$type] . ' (' . $type . ')';
    }
    /**
     * Dump node position, if possible.
     *
     * @param Node $node Node for which to dump position
     *
     * @return string|null Dump of position, or null if position information not available
     */
    protected function dumpPosition(\ConfigTransformer20210601\PhpParser\Node $node)
    {
        if (!$node->hasAttribute('startLine') || !$node->hasAttribute('endLine')) {
            return null;
        }
        $start = $node->getStartLine();
        $end = $node->getEndLine();
        if ($node->hasAttribute('startFilePos') && $node->hasAttribute('endFilePos') && null !== $this->code) {
            $start .= ':' . $this->toColumn($this->code, $node->getStartFilePos());
            $end .= ':' . $this->toColumn($this->code, $node->getEndFilePos());
        }
        return "[{$start} - {$end}]";
    }
    // Copied from Error class
    private function toColumn($code, $pos)
    {
        if ($pos > \strlen($code)) {
            throw new \RuntimeException('Invalid position information');
        }
        $lineStartPos = \strrpos($code, "\n", $pos - \strlen($code));
        if (\false === $lineStartPos) {
            $lineStartPos = -1;
        }
        return $pos - $lineStartPos;
    }
}
