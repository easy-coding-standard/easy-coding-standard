<?php

declare (strict_types=1);
namespace ECSPrefix20210803\PhpParser\PrettyPrinter;

use ECSPrefix20210803\PhpParser\Node;
use ECSPrefix20210803\PhpParser\Node\Expr;
use ECSPrefix20210803\PhpParser\Node\Expr\AssignOp;
use ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp;
use ECSPrefix20210803\PhpParser\Node\Expr\Cast;
use ECSPrefix20210803\PhpParser\Node\Name;
use ECSPrefix20210803\PhpParser\Node\Scalar;
use ECSPrefix20210803\PhpParser\Node\Scalar\MagicConst;
use ECSPrefix20210803\PhpParser\Node\Stmt;
use ECSPrefix20210803\PhpParser\PrettyPrinterAbstract;
class Standard extends \ECSPrefix20210803\PhpParser\PrettyPrinterAbstract
{
    // Special nodes
    protected function pParam(\ECSPrefix20210803\PhpParser\Node\Param $node)
    {
        return $this->pAttrGroups($node->attrGroups, \true) . $this->pModifiers($node->flags) . ($node->type ? $this->p($node->type) . ' ' : '') . ($node->byRef ? '&' : '') . ($node->variadic ? '...' : '') . $this->p($node->var) . ($node->default ? ' = ' . $this->p($node->default) : '');
    }
    protected function pArg(\ECSPrefix20210803\PhpParser\Node\Arg $node)
    {
        return ($node->name ? $node->name->toString() . ': ' : '') . ($node->byRef ? '&' : '') . ($node->unpack ? '...' : '') . $this->p($node->value);
    }
    protected function pConst(\ECSPrefix20210803\PhpParser\Node\Const_ $node)
    {
        return $node->name . ' = ' . $this->p($node->value);
    }
    protected function pNullableType(\ECSPrefix20210803\PhpParser\Node\NullableType $node)
    {
        return '?' . $this->p($node->type);
    }
    protected function pUnionType(\ECSPrefix20210803\PhpParser\Node\UnionType $node)
    {
        return $this->pImplode($node->types, '|');
    }
    protected function pIdentifier(\ECSPrefix20210803\PhpParser\Node\Identifier $node)
    {
        return $node->name;
    }
    protected function pVarLikeIdentifier(\ECSPrefix20210803\PhpParser\Node\VarLikeIdentifier $node)
    {
        return '$' . $node->name;
    }
    protected function pAttribute(\ECSPrefix20210803\PhpParser\Node\Attribute $node)
    {
        return $this->p($node->name) . ($node->args ? '(' . $this->pCommaSeparated($node->args) . ')' : '');
    }
    protected function pAttributeGroup(\ECSPrefix20210803\PhpParser\Node\AttributeGroup $node)
    {
        return '#[' . $this->pCommaSeparated($node->attrs) . ']';
    }
    // Names
    protected function pName(\ECSPrefix20210803\PhpParser\Node\Name $node)
    {
        return \implode('\\', $node->parts);
    }
    protected function pName_FullyQualified(\ECSPrefix20210803\PhpParser\Node\Name\FullyQualified $node)
    {
        return '\\' . \implode('\\', $node->parts);
    }
    protected function pName_Relative(\ECSPrefix20210803\PhpParser\Node\Name\Relative $node)
    {
        return 'namespace\\' . \implode('\\', $node->parts);
    }
    // Magic Constants
    protected function pScalar_MagicConst_Class(\ECSPrefix20210803\PhpParser\Node\Scalar\MagicConst\Class_ $node)
    {
        return '__CLASS__';
    }
    protected function pScalar_MagicConst_Dir(\ECSPrefix20210803\PhpParser\Node\Scalar\MagicConst\Dir $node)
    {
        return '__DIR__';
    }
    protected function pScalar_MagicConst_File(\ECSPrefix20210803\PhpParser\Node\Scalar\MagicConst\File $node)
    {
        return '__FILE__';
    }
    protected function pScalar_MagicConst_Function(\ECSPrefix20210803\PhpParser\Node\Scalar\MagicConst\Function_ $node)
    {
        return '__FUNCTION__';
    }
    protected function pScalar_MagicConst_Line(\ECSPrefix20210803\PhpParser\Node\Scalar\MagicConst\Line $node)
    {
        return '__LINE__';
    }
    protected function pScalar_MagicConst_Method(\ECSPrefix20210803\PhpParser\Node\Scalar\MagicConst\Method $node)
    {
        return '__METHOD__';
    }
    protected function pScalar_MagicConst_Namespace(\ECSPrefix20210803\PhpParser\Node\Scalar\MagicConst\Namespace_ $node)
    {
        return '__NAMESPACE__';
    }
    protected function pScalar_MagicConst_Trait(\ECSPrefix20210803\PhpParser\Node\Scalar\MagicConst\Trait_ $node)
    {
        return '__TRAIT__';
    }
    // Scalars
    protected function pScalar_String(\ECSPrefix20210803\PhpParser\Node\Scalar\String_ $node)
    {
        $kind = $node->getAttribute('kind', \ECSPrefix20210803\PhpParser\Node\Scalar\String_::KIND_SINGLE_QUOTED);
        switch ($kind) {
            case \ECSPrefix20210803\PhpParser\Node\Scalar\String_::KIND_NOWDOC:
                $label = $node->getAttribute('docLabel');
                if ($label && !$this->containsEndLabel($node->value, $label)) {
                    if ($node->value === '') {
                        return "<<<'{$label}'\n{$label}" . $this->docStringEndToken;
                    }
                    return "<<<'{$label}'\n{$node->value}\n{$label}" . $this->docStringEndToken;
                }
            /* break missing intentionally */
            case \ECSPrefix20210803\PhpParser\Node\Scalar\String_::KIND_SINGLE_QUOTED:
                return $this->pSingleQuotedString($node->value);
            case \ECSPrefix20210803\PhpParser\Node\Scalar\String_::KIND_HEREDOC:
                $label = $node->getAttribute('docLabel');
                if ($label && !$this->containsEndLabel($node->value, $label)) {
                    if ($node->value === '') {
                        return "<<<{$label}\n{$label}" . $this->docStringEndToken;
                    }
                    $escaped = $this->escapeString($node->value, null);
                    return "<<<{$label}\n" . $escaped . "\n{$label}" . $this->docStringEndToken;
                }
            /* break missing intentionally */
            case \ECSPrefix20210803\PhpParser\Node\Scalar\String_::KIND_DOUBLE_QUOTED:
                return '"' . $this->escapeString($node->value, '"') . '"';
        }
        throw new \Exception('Invalid string kind');
    }
    protected function pScalar_Encapsed(\ECSPrefix20210803\PhpParser\Node\Scalar\Encapsed $node)
    {
        if ($node->getAttribute('kind') === \ECSPrefix20210803\PhpParser\Node\Scalar\String_::KIND_HEREDOC) {
            $label = $node->getAttribute('docLabel');
            if ($label && !$this->encapsedContainsEndLabel($node->parts, $label)) {
                if (\count($node->parts) === 1 && $node->parts[0] instanceof \ECSPrefix20210803\PhpParser\Node\Scalar\EncapsedStringPart && $node->parts[0]->value === '') {
                    return "<<<{$label}\n{$label}" . $this->docStringEndToken;
                }
                return "<<<{$label}\n" . $this->pEncapsList($node->parts, null) . "\n{$label}" . $this->docStringEndToken;
            }
        }
        return '"' . $this->pEncapsList($node->parts, '"') . '"';
    }
    protected function pScalar_LNumber(\ECSPrefix20210803\PhpParser\Node\Scalar\LNumber $node)
    {
        if ($node->value === -\PHP_INT_MAX - 1) {
            // PHP_INT_MIN cannot be represented as a literal,
            // because the sign is not part of the literal
            return '(-' . \PHP_INT_MAX . '-1)';
        }
        $kind = $node->getAttribute('kind', \ECSPrefix20210803\PhpParser\Node\Scalar\LNumber::KIND_DEC);
        if (\ECSPrefix20210803\PhpParser\Node\Scalar\LNumber::KIND_DEC === $kind) {
            return (string) $node->value;
        }
        if ($node->value < 0) {
            $sign = '-';
            $str = (string) -$node->value;
        } else {
            $sign = '';
            $str = (string) $node->value;
        }
        switch ($kind) {
            case \ECSPrefix20210803\PhpParser\Node\Scalar\LNumber::KIND_BIN:
                return $sign . '0b' . \base_convert($str, 10, 2);
            case \ECSPrefix20210803\PhpParser\Node\Scalar\LNumber::KIND_OCT:
                return $sign . '0' . \base_convert($str, 10, 8);
            case \ECSPrefix20210803\PhpParser\Node\Scalar\LNumber::KIND_HEX:
                return $sign . '0x' . \base_convert($str, 10, 16);
        }
        throw new \Exception('Invalid number kind');
    }
    protected function pScalar_DNumber(\ECSPrefix20210803\PhpParser\Node\Scalar\DNumber $node)
    {
        if (!\is_finite($node->value)) {
            if ($node->value === \INF) {
                return '\\INF';
            } elseif ($node->value === -\INF) {
                return '-\\INF';
            } else {
                return '\\NAN';
            }
        }
        // Try to find a short full-precision representation
        $stringValue = \sprintf('%.16G', $node->value);
        if ($node->value !== (double) $stringValue) {
            $stringValue = \sprintf('%.17G', $node->value);
        }
        // %G is locale dependent and there exists no locale-independent alternative. We don't want
        // mess with switching locales here, so let's assume that a comma is the only non-standard
        // decimal separator we may encounter...
        $stringValue = \str_replace(',', '.', $stringValue);
        // ensure that number is really printed as float
        return \preg_match('/^-?[0-9]+$/', $stringValue) ? $stringValue . '.0' : $stringValue;
    }
    protected function pScalar_EncapsedStringPart(\ECSPrefix20210803\PhpParser\Node\Scalar\EncapsedStringPart $node)
    {
        throw new \LogicException('Cannot directly print EncapsedStringPart');
    }
    // Assignments
    protected function pExpr_Assign(\ECSPrefix20210803\PhpParser\Node\Expr\Assign $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\Assign::class, $node->var, ' = ', $node->expr);
    }
    protected function pExpr_AssignRef(\ECSPrefix20210803\PhpParser\Node\Expr\AssignRef $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\AssignRef::class, $node->var, ' =& ', $node->expr);
    }
    protected function pExpr_AssignOp_Plus(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Plus $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Plus::class, $node->var, ' += ', $node->expr);
    }
    protected function pExpr_AssignOp_Minus(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Minus $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Minus::class, $node->var, ' -= ', $node->expr);
    }
    protected function pExpr_AssignOp_Mul(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Mul $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Mul::class, $node->var, ' *= ', $node->expr);
    }
    protected function pExpr_AssignOp_Div(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Div $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Div::class, $node->var, ' /= ', $node->expr);
    }
    protected function pExpr_AssignOp_Concat(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Concat $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Concat::class, $node->var, ' .= ', $node->expr);
    }
    protected function pExpr_AssignOp_Mod(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Mod $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Mod::class, $node->var, ' %= ', $node->expr);
    }
    protected function pExpr_AssignOp_BitwiseAnd(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\BitwiseAnd $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\BitwiseAnd::class, $node->var, ' &= ', $node->expr);
    }
    protected function pExpr_AssignOp_BitwiseOr(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\BitwiseOr $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\BitwiseOr::class, $node->var, ' |= ', $node->expr);
    }
    protected function pExpr_AssignOp_BitwiseXor(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\BitwiseXor $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\BitwiseXor::class, $node->var, ' ^= ', $node->expr);
    }
    protected function pExpr_AssignOp_ShiftLeft(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\ShiftLeft $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\ShiftLeft::class, $node->var, ' <<= ', $node->expr);
    }
    protected function pExpr_AssignOp_ShiftRight(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\ShiftRight $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\ShiftRight::class, $node->var, ' >>= ', $node->expr);
    }
    protected function pExpr_AssignOp_Pow(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Pow $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Pow::class, $node->var, ' **= ', $node->expr);
    }
    protected function pExpr_AssignOp_Coalesce(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Coalesce $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\AssignOp\Coalesce::class, $node->var, ' ??= ', $node->expr);
    }
    // Binary expressions
    protected function pExpr_BinaryOp_Plus(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Plus $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Plus::class, $node->left, ' + ', $node->right);
    }
    protected function pExpr_BinaryOp_Minus(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Minus $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Minus::class, $node->left, ' - ', $node->right);
    }
    protected function pExpr_BinaryOp_Mul(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Mul $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Mul::class, $node->left, ' * ', $node->right);
    }
    protected function pExpr_BinaryOp_Div(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Div $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Div::class, $node->left, ' / ', $node->right);
    }
    protected function pExpr_BinaryOp_Concat(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Concat $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Concat::class, $node->left, ' . ', $node->right);
    }
    protected function pExpr_BinaryOp_Mod(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Mod $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Mod::class, $node->left, ' % ', $node->right);
    }
    protected function pExpr_BinaryOp_BooleanAnd(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\BooleanAnd $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\BooleanAnd::class, $node->left, ' && ', $node->right);
    }
    protected function pExpr_BinaryOp_BooleanOr(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\BooleanOr $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\BooleanOr::class, $node->left, ' || ', $node->right);
    }
    protected function pExpr_BinaryOp_BitwiseAnd(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\BitwiseAnd $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\BitwiseAnd::class, $node->left, ' & ', $node->right);
    }
    protected function pExpr_BinaryOp_BitwiseOr(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\BitwiseOr $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\BitwiseOr::class, $node->left, ' | ', $node->right);
    }
    protected function pExpr_BinaryOp_BitwiseXor(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\BitwiseXor $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\BitwiseXor::class, $node->left, ' ^ ', $node->right);
    }
    protected function pExpr_BinaryOp_ShiftLeft(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\ShiftLeft $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\ShiftLeft::class, $node->left, ' << ', $node->right);
    }
    protected function pExpr_BinaryOp_ShiftRight(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\ShiftRight $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\ShiftRight::class, $node->left, ' >> ', $node->right);
    }
    protected function pExpr_BinaryOp_Pow(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Pow $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Pow::class, $node->left, ' ** ', $node->right);
    }
    protected function pExpr_BinaryOp_LogicalAnd(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\LogicalAnd $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\LogicalAnd::class, $node->left, ' and ', $node->right);
    }
    protected function pExpr_BinaryOp_LogicalOr(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\LogicalOr $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\LogicalOr::class, $node->left, ' or ', $node->right);
    }
    protected function pExpr_BinaryOp_LogicalXor(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\LogicalXor $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\LogicalXor::class, $node->left, ' xor ', $node->right);
    }
    protected function pExpr_BinaryOp_Equal(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Equal $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Equal::class, $node->left, ' == ', $node->right);
    }
    protected function pExpr_BinaryOp_NotEqual(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\NotEqual $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\NotEqual::class, $node->left, ' != ', $node->right);
    }
    protected function pExpr_BinaryOp_Identical(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Identical $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Identical::class, $node->left, ' === ', $node->right);
    }
    protected function pExpr_BinaryOp_NotIdentical(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\NotIdentical $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\NotIdentical::class, $node->left, ' !== ', $node->right);
    }
    protected function pExpr_BinaryOp_Spaceship(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Spaceship $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Spaceship::class, $node->left, ' <=> ', $node->right);
    }
    protected function pExpr_BinaryOp_Greater(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Greater $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Greater::class, $node->left, ' > ', $node->right);
    }
    protected function pExpr_BinaryOp_GreaterOrEqual(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\GreaterOrEqual $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\GreaterOrEqual::class, $node->left, ' >= ', $node->right);
    }
    protected function pExpr_BinaryOp_Smaller(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Smaller $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Smaller::class, $node->left, ' < ', $node->right);
    }
    protected function pExpr_BinaryOp_SmallerOrEqual(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\SmallerOrEqual $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\SmallerOrEqual::class, $node->left, ' <= ', $node->right);
    }
    protected function pExpr_BinaryOp_Coalesce(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Coalesce $node)
    {
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BinaryOp\Coalesce::class, $node->left, ' ?? ', $node->right);
    }
    protected function pExpr_Instanceof(\ECSPrefix20210803\PhpParser\Node\Expr\Instanceof_ $node)
    {
        list($precedence, $associativity) = $this->precedenceMap[\ECSPrefix20210803\PhpParser\Node\Expr\Instanceof_::class];
        return $this->pPrec($node->expr, $precedence, $associativity, -1) . ' instanceof ' . $this->pNewVariable($node->class);
    }
    // Unary expressions
    protected function pExpr_BooleanNot(\ECSPrefix20210803\PhpParser\Node\Expr\BooleanNot $node)
    {
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BooleanNot::class, '!', $node->expr);
    }
    protected function pExpr_BitwiseNot(\ECSPrefix20210803\PhpParser\Node\Expr\BitwiseNot $node)
    {
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\BitwiseNot::class, '~', $node->expr);
    }
    protected function pExpr_UnaryMinus(\ECSPrefix20210803\PhpParser\Node\Expr\UnaryMinus $node)
    {
        if ($node->expr instanceof \ECSPrefix20210803\PhpParser\Node\Expr\UnaryMinus || $node->expr instanceof \ECSPrefix20210803\PhpParser\Node\Expr\PreDec) {
            // Enforce -(-$expr) instead of --$expr
            return '-(' . $this->p($node->expr) . ')';
        }
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\UnaryMinus::class, '-', $node->expr);
    }
    protected function pExpr_UnaryPlus(\ECSPrefix20210803\PhpParser\Node\Expr\UnaryPlus $node)
    {
        if ($node->expr instanceof \ECSPrefix20210803\PhpParser\Node\Expr\UnaryPlus || $node->expr instanceof \ECSPrefix20210803\PhpParser\Node\Expr\PreInc) {
            // Enforce +(+$expr) instead of ++$expr
            return '+(' . $this->p($node->expr) . ')';
        }
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\UnaryPlus::class, '+', $node->expr);
    }
    protected function pExpr_PreInc(\ECSPrefix20210803\PhpParser\Node\Expr\PreInc $node)
    {
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\PreInc::class, '++', $node->var);
    }
    protected function pExpr_PreDec(\ECSPrefix20210803\PhpParser\Node\Expr\PreDec $node)
    {
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\PreDec::class, '--', $node->var);
    }
    protected function pExpr_PostInc(\ECSPrefix20210803\PhpParser\Node\Expr\PostInc $node)
    {
        return $this->pPostfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\PostInc::class, $node->var, '++');
    }
    protected function pExpr_PostDec(\ECSPrefix20210803\PhpParser\Node\Expr\PostDec $node)
    {
        return $this->pPostfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\PostDec::class, $node->var, '--');
    }
    protected function pExpr_ErrorSuppress(\ECSPrefix20210803\PhpParser\Node\Expr\ErrorSuppress $node)
    {
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\ErrorSuppress::class, '@', $node->expr);
    }
    protected function pExpr_YieldFrom(\ECSPrefix20210803\PhpParser\Node\Expr\YieldFrom $node)
    {
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\YieldFrom::class, 'yield from ', $node->expr);
    }
    protected function pExpr_Print(\ECSPrefix20210803\PhpParser\Node\Expr\Print_ $node)
    {
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\Print_::class, 'print ', $node->expr);
    }
    // Casts
    protected function pExpr_Cast_Int(\ECSPrefix20210803\PhpParser\Node\Expr\Cast\Int_ $node)
    {
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\Cast\Int_::class, '(int) ', $node->expr);
    }
    protected function pExpr_Cast_Double(\ECSPrefix20210803\PhpParser\Node\Expr\Cast\Double $node)
    {
        $kind = $node->getAttribute('kind', \ECSPrefix20210803\PhpParser\Node\Expr\Cast\Double::KIND_DOUBLE);
        if ($kind === \ECSPrefix20210803\PhpParser\Node\Expr\Cast\Double::KIND_DOUBLE) {
            $cast = '(double)';
        } elseif ($kind === \ECSPrefix20210803\PhpParser\Node\Expr\Cast\Double::KIND_FLOAT) {
            $cast = '(float)';
        } elseif ($kind === \ECSPrefix20210803\PhpParser\Node\Expr\Cast\Double::KIND_REAL) {
            $cast = '(real)';
        }
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\Cast\Double::class, $cast . ' ', $node->expr);
    }
    protected function pExpr_Cast_String(\ECSPrefix20210803\PhpParser\Node\Expr\Cast\String_ $node)
    {
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\Cast\String_::class, '(string) ', $node->expr);
    }
    protected function pExpr_Cast_Array(\ECSPrefix20210803\PhpParser\Node\Expr\Cast\Array_ $node)
    {
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\Cast\Array_::class, '(array) ', $node->expr);
    }
    protected function pExpr_Cast_Object(\ECSPrefix20210803\PhpParser\Node\Expr\Cast\Object_ $node)
    {
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\Cast\Object_::class, '(object) ', $node->expr);
    }
    protected function pExpr_Cast_Bool(\ECSPrefix20210803\PhpParser\Node\Expr\Cast\Bool_ $node)
    {
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\Cast\Bool_::class, '(bool) ', $node->expr);
    }
    protected function pExpr_Cast_Unset(\ECSPrefix20210803\PhpParser\Node\Expr\Cast\Unset_ $node)
    {
        return $this->pPrefixOp(\ECSPrefix20210803\PhpParser\Node\Expr\Cast\Unset_::class, '(unset) ', $node->expr);
    }
    // Function calls and similar constructs
    protected function pExpr_FuncCall(\ECSPrefix20210803\PhpParser\Node\Expr\FuncCall $node)
    {
        return $this->pCallLhs($node->name) . '(' . $this->pMaybeMultiline($node->args) . ')';
    }
    protected function pExpr_MethodCall(\ECSPrefix20210803\PhpParser\Node\Expr\MethodCall $node)
    {
        return $this->pDereferenceLhs($node->var) . '->' . $this->pObjectProperty($node->name) . '(' . $this->pMaybeMultiline($node->args) . ')';
    }
    protected function pExpr_NullsafeMethodCall(\ECSPrefix20210803\PhpParser\Node\Expr\NullsafeMethodCall $node)
    {
        return $this->pDereferenceLhs($node->var) . '?->' . $this->pObjectProperty($node->name) . '(' . $this->pMaybeMultiline($node->args) . ')';
    }
    protected function pExpr_StaticCall(\ECSPrefix20210803\PhpParser\Node\Expr\StaticCall $node)
    {
        return $this->pDereferenceLhs($node->class) . '::' . ($node->name instanceof \ECSPrefix20210803\PhpParser\Node\Expr ? $node->name instanceof \ECSPrefix20210803\PhpParser\Node\Expr\Variable ? $this->p($node->name) : '{' . $this->p($node->name) . '}' : $node->name) . '(' . $this->pMaybeMultiline($node->args) . ')';
    }
    protected function pExpr_Empty(\ECSPrefix20210803\PhpParser\Node\Expr\Empty_ $node)
    {
        return 'empty(' . $this->p($node->expr) . ')';
    }
    protected function pExpr_Isset(\ECSPrefix20210803\PhpParser\Node\Expr\Isset_ $node)
    {
        return 'isset(' . $this->pCommaSeparated($node->vars) . ')';
    }
    protected function pExpr_Eval(\ECSPrefix20210803\PhpParser\Node\Expr\Eval_ $node)
    {
        return 'eval(' . $this->p($node->expr) . ')';
    }
    protected function pExpr_Include(\ECSPrefix20210803\PhpParser\Node\Expr\Include_ $node)
    {
        static $map = [\ECSPrefix20210803\PhpParser\Node\Expr\Include_::TYPE_INCLUDE => 'include', \ECSPrefix20210803\PhpParser\Node\Expr\Include_::TYPE_INCLUDE_ONCE => 'include_once', \ECSPrefix20210803\PhpParser\Node\Expr\Include_::TYPE_REQUIRE => 'require', \ECSPrefix20210803\PhpParser\Node\Expr\Include_::TYPE_REQUIRE_ONCE => 'require_once'];
        return $map[$node->type] . ' ' . $this->p($node->expr);
    }
    protected function pExpr_List(\ECSPrefix20210803\PhpParser\Node\Expr\List_ $node)
    {
        return 'list(' . $this->pCommaSeparated($node->items) . ')';
    }
    // Other
    protected function pExpr_Error(\ECSPrefix20210803\PhpParser\Node\Expr\Error $node)
    {
        throw new \LogicException('Cannot pretty-print AST with Error nodes');
    }
    protected function pExpr_Variable(\ECSPrefix20210803\PhpParser\Node\Expr\Variable $node)
    {
        if ($node->name instanceof \ECSPrefix20210803\PhpParser\Node\Expr) {
            return '${' . $this->p($node->name) . '}';
        } else {
            return '$' . $node->name;
        }
    }
    protected function pExpr_Array(\ECSPrefix20210803\PhpParser\Node\Expr\Array_ $node)
    {
        $syntax = $node->getAttribute('kind', $this->options['shortArraySyntax'] ? \ECSPrefix20210803\PhpParser\Node\Expr\Array_::KIND_SHORT : \ECSPrefix20210803\PhpParser\Node\Expr\Array_::KIND_LONG);
        if ($syntax === \ECSPrefix20210803\PhpParser\Node\Expr\Array_::KIND_SHORT) {
            return '[' . $this->pMaybeMultiline($node->items, \true) . ']';
        } else {
            return 'array(' . $this->pMaybeMultiline($node->items, \true) . ')';
        }
    }
    protected function pExpr_ArrayItem(\ECSPrefix20210803\PhpParser\Node\Expr\ArrayItem $node)
    {
        return (null !== $node->key ? $this->p($node->key) . ' => ' : '') . ($node->byRef ? '&' : '') . ($node->unpack ? '...' : '') . $this->p($node->value);
    }
    protected function pExpr_ArrayDimFetch(\ECSPrefix20210803\PhpParser\Node\Expr\ArrayDimFetch $node)
    {
        return $this->pDereferenceLhs($node->var) . '[' . (null !== $node->dim ? $this->p($node->dim) : '') . ']';
    }
    protected function pExpr_ConstFetch(\ECSPrefix20210803\PhpParser\Node\Expr\ConstFetch $node)
    {
        return $this->p($node->name);
    }
    protected function pExpr_ClassConstFetch(\ECSPrefix20210803\PhpParser\Node\Expr\ClassConstFetch $node)
    {
        return $this->pDereferenceLhs($node->class) . '::' . $this->p($node->name);
    }
    protected function pExpr_PropertyFetch(\ECSPrefix20210803\PhpParser\Node\Expr\PropertyFetch $node)
    {
        return $this->pDereferenceLhs($node->var) . '->' . $this->pObjectProperty($node->name);
    }
    protected function pExpr_NullsafePropertyFetch(\ECSPrefix20210803\PhpParser\Node\Expr\NullsafePropertyFetch $node)
    {
        return $this->pDereferenceLhs($node->var) . '?->' . $this->pObjectProperty($node->name);
    }
    protected function pExpr_StaticPropertyFetch(\ECSPrefix20210803\PhpParser\Node\Expr\StaticPropertyFetch $node)
    {
        return $this->pDereferenceLhs($node->class) . '::$' . $this->pObjectProperty($node->name);
    }
    protected function pExpr_ShellExec(\ECSPrefix20210803\PhpParser\Node\Expr\ShellExec $node)
    {
        return '`' . $this->pEncapsList($node->parts, '`') . '`';
    }
    protected function pExpr_Closure(\ECSPrefix20210803\PhpParser\Node\Expr\Closure $node)
    {
        return $this->pAttrGroups($node->attrGroups, \true) . ($node->static ? 'static ' : '') . 'function ' . ($node->byRef ? '&' : '') . '(' . $this->pCommaSeparated($node->params) . ')' . (!empty($node->uses) ? ' use(' . $this->pCommaSeparated($node->uses) . ')' : '') . (null !== $node->returnType ? ' : ' . $this->p($node->returnType) : '') . ' {' . $this->pStmts($node->stmts) . $this->nl . '}';
    }
    protected function pExpr_Match(\ECSPrefix20210803\PhpParser\Node\Expr\Match_ $node)
    {
        return 'match (' . $this->p($node->cond) . ') {' . $this->pCommaSeparatedMultiline($node->arms, \true) . $this->nl . '}';
    }
    protected function pMatchArm(\ECSPrefix20210803\PhpParser\Node\MatchArm $node)
    {
        return ($node->conds ? $this->pCommaSeparated($node->conds) : 'default') . ' => ' . $this->p($node->body);
    }
    protected function pExpr_ArrowFunction(\ECSPrefix20210803\PhpParser\Node\Expr\ArrowFunction $node)
    {
        return $this->pAttrGroups($node->attrGroups, \true) . ($node->static ? 'static ' : '') . 'fn' . ($node->byRef ? '&' : '') . '(' . $this->pCommaSeparated($node->params) . ')' . (null !== $node->returnType ? ': ' . $this->p($node->returnType) : '') . ' => ' . $this->p($node->expr);
    }
    protected function pExpr_ClosureUse(\ECSPrefix20210803\PhpParser\Node\Expr\ClosureUse $node)
    {
        return ($node->byRef ? '&' : '') . $this->p($node->var);
    }
    protected function pExpr_New(\ECSPrefix20210803\PhpParser\Node\Expr\New_ $node)
    {
        if ($node->class instanceof \ECSPrefix20210803\PhpParser\Node\Stmt\Class_) {
            $args = $node->args ? '(' . $this->pMaybeMultiline($node->args) . ')' : '';
            return 'new ' . $this->pClassCommon($node->class, $args);
        }
        return 'new ' . $this->pNewVariable($node->class) . '(' . $this->pMaybeMultiline($node->args) . ')';
    }
    protected function pExpr_Clone(\ECSPrefix20210803\PhpParser\Node\Expr\Clone_ $node)
    {
        return 'clone ' . $this->p($node->expr);
    }
    protected function pExpr_Ternary(\ECSPrefix20210803\PhpParser\Node\Expr\Ternary $node)
    {
        // a bit of cheating: we treat the ternary as a binary op where the ?...: part is the operator.
        // this is okay because the part between ? and : never needs parentheses.
        return $this->pInfixOp(\ECSPrefix20210803\PhpParser\Node\Expr\Ternary::class, $node->cond, ' ?' . (null !== $node->if ? ' ' . $this->p($node->if) . ' ' : '') . ': ', $node->else);
    }
    protected function pExpr_Exit(\ECSPrefix20210803\PhpParser\Node\Expr\Exit_ $node)
    {
        $kind = $node->getAttribute('kind', \ECSPrefix20210803\PhpParser\Node\Expr\Exit_::KIND_DIE);
        return ($kind === \ECSPrefix20210803\PhpParser\Node\Expr\Exit_::KIND_EXIT ? 'exit' : 'die') . (null !== $node->expr ? '(' . $this->p($node->expr) . ')' : '');
    }
    protected function pExpr_Throw(\ECSPrefix20210803\PhpParser\Node\Expr\Throw_ $node)
    {
        return 'throw ' . $this->p($node->expr);
    }
    protected function pExpr_Yield(\ECSPrefix20210803\PhpParser\Node\Expr\Yield_ $node)
    {
        if ($node->value === null) {
            return 'yield';
        } else {
            // this is a bit ugly, but currently there is no way to detect whether the parentheses are necessary
            return '(yield ' . ($node->key !== null ? $this->p($node->key) . ' => ' : '') . $this->p($node->value) . ')';
        }
    }
    // Declarations
    protected function pStmt_Namespace(\ECSPrefix20210803\PhpParser\Node\Stmt\Namespace_ $node)
    {
        if ($this->canUseSemicolonNamespaces) {
            return 'namespace ' . $this->p($node->name) . ';' . $this->nl . $this->pStmts($node->stmts, \false);
        } else {
            return 'namespace' . (null !== $node->name ? ' ' . $this->p($node->name) : '') . ' {' . $this->pStmts($node->stmts) . $this->nl . '}';
        }
    }
    protected function pStmt_Use(\ECSPrefix20210803\PhpParser\Node\Stmt\Use_ $node)
    {
        return 'use ' . $this->pUseType($node->type) . $this->pCommaSeparated($node->uses) . ';';
    }
    protected function pStmt_GroupUse(\ECSPrefix20210803\PhpParser\Node\Stmt\GroupUse $node)
    {
        return 'use ' . $this->pUseType($node->type) . $this->pName($node->prefix) . '\\{' . $this->pCommaSeparated($node->uses) . '};';
    }
    protected function pStmt_UseUse(\ECSPrefix20210803\PhpParser\Node\Stmt\UseUse $node)
    {
        return $this->pUseType($node->type) . $this->p($node->name) . (null !== $node->alias ? ' as ' . $node->alias : '');
    }
    protected function pUseType($type)
    {
        return $type === \ECSPrefix20210803\PhpParser\Node\Stmt\Use_::TYPE_FUNCTION ? 'function ' : ($type === \ECSPrefix20210803\PhpParser\Node\Stmt\Use_::TYPE_CONSTANT ? 'const ' : '');
    }
    protected function pStmt_Interface(\ECSPrefix20210803\PhpParser\Node\Stmt\Interface_ $node)
    {
        return $this->pAttrGroups($node->attrGroups) . 'interface ' . $node->name . (!empty($node->extends) ? ' extends ' . $this->pCommaSeparated($node->extends) : '') . $this->nl . '{' . $this->pStmts($node->stmts) . $this->nl . '}';
    }
    protected function pStmt_Enum(\ECSPrefix20210803\PhpParser\Node\Stmt\Enum_ $node)
    {
        return $this->pAttrGroups($node->attrGroups) . 'enum ' . $node->name . ($node->scalarType ? " : {$node->scalarType}" : '') . (!empty($node->implements) ? ' implements ' . $this->pCommaSeparated($node->implements) : '') . $this->nl . '{' . $this->pStmts($node->stmts) . $this->nl . '}';
    }
    protected function pStmt_Class(\ECSPrefix20210803\PhpParser\Node\Stmt\Class_ $node)
    {
        return $this->pClassCommon($node, ' ' . $node->name);
    }
    protected function pStmt_Trait(\ECSPrefix20210803\PhpParser\Node\Stmt\Trait_ $node)
    {
        return $this->pAttrGroups($node->attrGroups) . 'trait ' . $node->name . $this->nl . '{' . $this->pStmts($node->stmts) . $this->nl . '}';
    }
    protected function pStmt_EnumCase(\ECSPrefix20210803\PhpParser\Node\Stmt\EnumCase $node)
    {
        return $this->pAttrGroups($node->attrGroups) . 'case ' . $node->name . ($node->expr ? ' = ' . $this->p($node->expr) : '') . ';';
    }
    protected function pStmt_TraitUse(\ECSPrefix20210803\PhpParser\Node\Stmt\TraitUse $node)
    {
        return 'use ' . $this->pCommaSeparated($node->traits) . (empty($node->adaptations) ? ';' : ' {' . $this->pStmts($node->adaptations) . $this->nl . '}');
    }
    protected function pStmt_TraitUseAdaptation_Precedence(\ECSPrefix20210803\PhpParser\Node\Stmt\TraitUseAdaptation\Precedence $node)
    {
        return $this->p($node->trait) . '::' . $node->method . ' insteadof ' . $this->pCommaSeparated($node->insteadof) . ';';
    }
    protected function pStmt_TraitUseAdaptation_Alias(\ECSPrefix20210803\PhpParser\Node\Stmt\TraitUseAdaptation\Alias $node)
    {
        return (null !== $node->trait ? $this->p($node->trait) . '::' : '') . $node->method . ' as' . (null !== $node->newModifier ? ' ' . \rtrim($this->pModifiers($node->newModifier), ' ') : '') . (null !== $node->newName ? ' ' . $node->newName : '') . ';';
    }
    protected function pStmt_Property(\ECSPrefix20210803\PhpParser\Node\Stmt\Property $node)
    {
        return $this->pAttrGroups($node->attrGroups) . (0 === $node->flags ? 'var ' : $this->pModifiers($node->flags)) . ($node->type ? $this->p($node->type) . ' ' : '') . $this->pCommaSeparated($node->props) . ';';
    }
    protected function pStmt_PropertyProperty(\ECSPrefix20210803\PhpParser\Node\Stmt\PropertyProperty $node)
    {
        return '$' . $node->name . (null !== $node->default ? ' = ' . $this->p($node->default) : '');
    }
    protected function pStmt_ClassMethod(\ECSPrefix20210803\PhpParser\Node\Stmt\ClassMethod $node)
    {
        return $this->pAttrGroups($node->attrGroups) . $this->pModifiers($node->flags) . 'function ' . ($node->byRef ? '&' : '') . $node->name . '(' . $this->pMaybeMultiline($node->params) . ')' . (null !== $node->returnType ? ' : ' . $this->p($node->returnType) : '') . (null !== $node->stmts ? $this->nl . '{' . $this->pStmts($node->stmts) . $this->nl . '}' : ';');
    }
    protected function pStmt_ClassConst(\ECSPrefix20210803\PhpParser\Node\Stmt\ClassConst $node)
    {
        return $this->pAttrGroups($node->attrGroups) . $this->pModifiers($node->flags) . 'const ' . $this->pCommaSeparated($node->consts) . ';';
    }
    protected function pStmt_Function(\ECSPrefix20210803\PhpParser\Node\Stmt\Function_ $node)
    {
        return $this->pAttrGroups($node->attrGroups) . 'function ' . ($node->byRef ? '&' : '') . $node->name . '(' . $this->pCommaSeparated($node->params) . ')' . (null !== $node->returnType ? ' : ' . $this->p($node->returnType) : '') . $this->nl . '{' . $this->pStmts($node->stmts) . $this->nl . '}';
    }
    protected function pStmt_Const(\ECSPrefix20210803\PhpParser\Node\Stmt\Const_ $node)
    {
        return 'const ' . $this->pCommaSeparated($node->consts) . ';';
    }
    protected function pStmt_Declare(\ECSPrefix20210803\PhpParser\Node\Stmt\Declare_ $node)
    {
        return 'declare (' . $this->pCommaSeparated($node->declares) . ')' . (null !== $node->stmts ? ' {' . $this->pStmts($node->stmts) . $this->nl . '}' : ';');
    }
    protected function pStmt_DeclareDeclare(\ECSPrefix20210803\PhpParser\Node\Stmt\DeclareDeclare $node)
    {
        return $node->key . '=' . $this->p($node->value);
    }
    // Control flow
    protected function pStmt_If(\ECSPrefix20210803\PhpParser\Node\Stmt\If_ $node)
    {
        return 'if (' . $this->p($node->cond) . ') {' . $this->pStmts($node->stmts) . $this->nl . '}' . ($node->elseifs ? ' ' . $this->pImplode($node->elseifs, ' ') : '') . (null !== $node->else ? ' ' . $this->p($node->else) : '');
    }
    protected function pStmt_ElseIf(\ECSPrefix20210803\PhpParser\Node\Stmt\ElseIf_ $node)
    {
        return 'elseif (' . $this->p($node->cond) . ') {' . $this->pStmts($node->stmts) . $this->nl . '}';
    }
    protected function pStmt_Else(\ECSPrefix20210803\PhpParser\Node\Stmt\Else_ $node)
    {
        return 'else {' . $this->pStmts($node->stmts) . $this->nl . '}';
    }
    protected function pStmt_For(\ECSPrefix20210803\PhpParser\Node\Stmt\For_ $node)
    {
        return 'for (' . $this->pCommaSeparated($node->init) . ';' . (!empty($node->cond) ? ' ' : '') . $this->pCommaSeparated($node->cond) . ';' . (!empty($node->loop) ? ' ' : '') . $this->pCommaSeparated($node->loop) . ') {' . $this->pStmts($node->stmts) . $this->nl . '}';
    }
    protected function pStmt_Foreach(\ECSPrefix20210803\PhpParser\Node\Stmt\Foreach_ $node)
    {
        return 'foreach (' . $this->p($node->expr) . ' as ' . (null !== $node->keyVar ? $this->p($node->keyVar) . ' => ' : '') . ($node->byRef ? '&' : '') . $this->p($node->valueVar) . ') {' . $this->pStmts($node->stmts) . $this->nl . '}';
    }
    protected function pStmt_While(\ECSPrefix20210803\PhpParser\Node\Stmt\While_ $node)
    {
        return 'while (' . $this->p($node->cond) . ') {' . $this->pStmts($node->stmts) . $this->nl . '}';
    }
    protected function pStmt_Do(\ECSPrefix20210803\PhpParser\Node\Stmt\Do_ $node)
    {
        return 'do {' . $this->pStmts($node->stmts) . $this->nl . '} while (' . $this->p($node->cond) . ');';
    }
    protected function pStmt_Switch(\ECSPrefix20210803\PhpParser\Node\Stmt\Switch_ $node)
    {
        return 'switch (' . $this->p($node->cond) . ') {' . $this->pStmts($node->cases) . $this->nl . '}';
    }
    protected function pStmt_Case(\ECSPrefix20210803\PhpParser\Node\Stmt\Case_ $node)
    {
        return (null !== $node->cond ? 'case ' . $this->p($node->cond) : 'default') . ':' . $this->pStmts($node->stmts);
    }
    protected function pStmt_TryCatch(\ECSPrefix20210803\PhpParser\Node\Stmt\TryCatch $node)
    {
        return 'try {' . $this->pStmts($node->stmts) . $this->nl . '}' . ($node->catches ? ' ' . $this->pImplode($node->catches, ' ') : '') . ($node->finally !== null ? ' ' . $this->p($node->finally) : '');
    }
    protected function pStmt_Catch(\ECSPrefix20210803\PhpParser\Node\Stmt\Catch_ $node)
    {
        return 'catch (' . $this->pImplode($node->types, '|') . ($node->var !== null ? ' ' . $this->p($node->var) : '') . ') {' . $this->pStmts($node->stmts) . $this->nl . '}';
    }
    protected function pStmt_Finally(\ECSPrefix20210803\PhpParser\Node\Stmt\Finally_ $node)
    {
        return 'finally {' . $this->pStmts($node->stmts) . $this->nl . '}';
    }
    protected function pStmt_Break(\ECSPrefix20210803\PhpParser\Node\Stmt\Break_ $node)
    {
        return 'break' . ($node->num !== null ? ' ' . $this->p($node->num) : '') . ';';
    }
    protected function pStmt_Continue(\ECSPrefix20210803\PhpParser\Node\Stmt\Continue_ $node)
    {
        return 'continue' . ($node->num !== null ? ' ' . $this->p($node->num) : '') . ';';
    }
    protected function pStmt_Return(\ECSPrefix20210803\PhpParser\Node\Stmt\Return_ $node)
    {
        return 'return' . (null !== $node->expr ? ' ' . $this->p($node->expr) : '') . ';';
    }
    protected function pStmt_Throw(\ECSPrefix20210803\PhpParser\Node\Stmt\Throw_ $node)
    {
        return 'throw ' . $this->p($node->expr) . ';';
    }
    protected function pStmt_Label(\ECSPrefix20210803\PhpParser\Node\Stmt\Label $node)
    {
        return $node->name . ':';
    }
    protected function pStmt_Goto(\ECSPrefix20210803\PhpParser\Node\Stmt\Goto_ $node)
    {
        return 'goto ' . $node->name . ';';
    }
    // Other
    protected function pStmt_Expression(\ECSPrefix20210803\PhpParser\Node\Stmt\Expression $node)
    {
        return $this->p($node->expr) . ';';
    }
    protected function pStmt_Echo(\ECSPrefix20210803\PhpParser\Node\Stmt\Echo_ $node)
    {
        return 'echo ' . $this->pCommaSeparated($node->exprs) . ';';
    }
    protected function pStmt_Static(\ECSPrefix20210803\PhpParser\Node\Stmt\Static_ $node)
    {
        return 'static ' . $this->pCommaSeparated($node->vars) . ';';
    }
    protected function pStmt_Global(\ECSPrefix20210803\PhpParser\Node\Stmt\Global_ $node)
    {
        return 'global ' . $this->pCommaSeparated($node->vars) . ';';
    }
    protected function pStmt_StaticVar(\ECSPrefix20210803\PhpParser\Node\Stmt\StaticVar $node)
    {
        return $this->p($node->var) . (null !== $node->default ? ' = ' . $this->p($node->default) : '');
    }
    protected function pStmt_Unset(\ECSPrefix20210803\PhpParser\Node\Stmt\Unset_ $node)
    {
        return 'unset(' . $this->pCommaSeparated($node->vars) . ');';
    }
    protected function pStmt_InlineHTML(\ECSPrefix20210803\PhpParser\Node\Stmt\InlineHTML $node)
    {
        $newline = $node->getAttribute('hasLeadingNewline', \true) ? "\n" : '';
        return '?>' . $newline . $node->value . '<?php ';
    }
    protected function pStmt_HaltCompiler(\ECSPrefix20210803\PhpParser\Node\Stmt\HaltCompiler $node)
    {
        return '__halt_compiler();' . $node->remaining;
    }
    protected function pStmt_Nop(\ECSPrefix20210803\PhpParser\Node\Stmt\Nop $node)
    {
        return '';
    }
    // Helpers
    protected function pClassCommon(\ECSPrefix20210803\PhpParser\Node\Stmt\Class_ $node, $afterClassToken)
    {
        return $this->pAttrGroups($node->attrGroups, $node->name === null) . $this->pModifiers($node->flags) . 'class' . $afterClassToken . (null !== $node->extends ? ' extends ' . $this->p($node->extends) : '') . (!empty($node->implements) ? ' implements ' . $this->pCommaSeparated($node->implements) : '') . $this->nl . '{' . $this->pStmts($node->stmts) . $this->nl . '}';
    }
    protected function pObjectProperty($node)
    {
        if ($node instanceof \ECSPrefix20210803\PhpParser\Node\Expr) {
            return '{' . $this->p($node) . '}';
        } else {
            return $node;
        }
    }
    protected function pEncapsList(array $encapsList, $quote)
    {
        $return = '';
        foreach ($encapsList as $element) {
            if ($element instanceof \ECSPrefix20210803\PhpParser\Node\Scalar\EncapsedStringPart) {
                $return .= $this->escapeString($element->value, $quote);
            } else {
                $return .= '{' . $this->p($element) . '}';
            }
        }
        return $return;
    }
    protected function pSingleQuotedString(string $string)
    {
        return '\'' . \addcslashes($string, '\'\\') . '\'';
    }
    protected function escapeString($string, $quote)
    {
        if (null === $quote) {
            // For doc strings, don't escape newlines
            $escaped = \addcslashes($string, "\t\f\v\$\\");
        } else {
            $escaped = \addcslashes($string, "\n\r\t\f\v\$" . $quote . "\\");
        }
        // Escape control characters and non-UTF-8 characters.
        // Regex based on https://stackoverflow.com/a/11709412/385378.
        $regex = '/(
              [\\x00-\\x08\\x0E-\\x1F] # Control characters
            | [\\xC0-\\xC1] # Invalid UTF-8 Bytes
            | [\\xF5-\\xFF] # Invalid UTF-8 Bytes
            | \\xE0(?=[\\x80-\\x9F]) # Overlong encoding of prior code point
            | \\xF0(?=[\\x80-\\x8F]) # Overlong encoding of prior code point
            | [\\xC2-\\xDF](?![\\x80-\\xBF]) # Invalid UTF-8 Sequence Start
            | [\\xE0-\\xEF](?![\\x80-\\xBF]{2}) # Invalid UTF-8 Sequence Start
            | [\\xF0-\\xF4](?![\\x80-\\xBF]{3}) # Invalid UTF-8 Sequence Start
            | (?<=[\\x00-\\x7F\\xF5-\\xFF])[\\x80-\\xBF] # Invalid UTF-8 Sequence Middle
            | (?<![\\xC2-\\xDF]|[\\xE0-\\xEF]|[\\xE0-\\xEF][\\x80-\\xBF]|[\\xF0-\\xF4]|[\\xF0-\\xF4][\\x80-\\xBF]|[\\xF0-\\xF4][\\x80-\\xBF]{2})[\\x80-\\xBF] # Overlong Sequence
            | (?<=[\\xE0-\\xEF])[\\x80-\\xBF](?![\\x80-\\xBF]) # Short 3 byte sequence
            | (?<=[\\xF0-\\xF4])[\\x80-\\xBF](?![\\x80-\\xBF]{2}) # Short 4 byte sequence
            | (?<=[\\xF0-\\xF4][\\x80-\\xBF])[\\x80-\\xBF](?![\\x80-\\xBF]) # Short 4 byte sequence (2)
        )/x';
        return \preg_replace_callback($regex, function ($matches) {
            \assert(\strlen($matches[0]) === 1);
            $hex = \dechex(\ord($matches[0]));
            return '\\x' . \str_pad($hex, 2, '0', \STR_PAD_LEFT);
        }, $escaped);
    }
    protected function containsEndLabel($string, $label, $atStart = \true, $atEnd = \true)
    {
        $start = $atStart ? '(?:^|[\\r\\n])' : '[\\r\\n]';
        $end = $atEnd ? '(?:$|[;\\r\\n])' : '[;\\r\\n]';
        return \false !== \strpos($string, $label) && \preg_match('/' . $start . $label . $end . '/', $string);
    }
    protected function encapsedContainsEndLabel(array $parts, $label)
    {
        foreach ($parts as $i => $part) {
            $atStart = $i === 0;
            $atEnd = $i === \count($parts) - 1;
            if ($part instanceof \ECSPrefix20210803\PhpParser\Node\Scalar\EncapsedStringPart && $this->containsEndLabel($part->value, $label, $atStart, $atEnd)) {
                return \true;
            }
        }
        return \false;
    }
    protected function pDereferenceLhs(\ECSPrefix20210803\PhpParser\Node $node)
    {
        if (!$this->dereferenceLhsRequiresParens($node)) {
            return $this->p($node);
        } else {
            return '(' . $this->p($node) . ')';
        }
    }
    protected function pCallLhs(\ECSPrefix20210803\PhpParser\Node $node)
    {
        if (!$this->callLhsRequiresParens($node)) {
            return $this->p($node);
        } else {
            return '(' . $this->p($node) . ')';
        }
    }
    protected function pNewVariable(\ECSPrefix20210803\PhpParser\Node $node)
    {
        // TODO: This is not fully accurate.
        return $this->pDereferenceLhs($node);
    }
    /**
     * @param Node[] $nodes
     * @return bool
     */
    protected function hasNodeWithComments(array $nodes)
    {
        foreach ($nodes as $node) {
            if ($node && $node->getComments()) {
                return \true;
            }
        }
        return \false;
    }
    protected function pMaybeMultiline(array $nodes, bool $trailingComma = \false)
    {
        if (!$this->hasNodeWithComments($nodes)) {
            return $this->pCommaSeparated($nodes);
        } else {
            return $this->pCommaSeparatedMultiline($nodes, $trailingComma) . $this->nl;
        }
    }
    protected function pAttrGroups(array $nodes, bool $inline = \false) : string
    {
        $result = '';
        $sep = $inline ? ' ' : $this->nl;
        foreach ($nodes as $node) {
            $result .= $this->p($node) . $sep;
        }
        return $result;
    }
}
