<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\VarDumper\Caster;

use FFI\CData;
use FFI\CType;
use ECSPrefix202306\Symfony\Component\VarDumper\Cloner\Stub;
/**
 * Casts FFI extension classes to array representation.
 *
 * @author Nesmeyanov Kirill <nesk@xakep.ru>
 */
final class FFICaster
{
    /**
     * In case of "char*" contains a string, the length of which depends on
     * some other parameter, then during the generation of the string it is
     * possible to go beyond the allowable memory area.
     *
     * This restriction serves to ensure that processing does not take
     * up the entire allowable PHP memory limit.
     */
    private const MAX_STRING_LENGTH = 255;
    /**
     * @param \FFI\CData|\FFI\CType $data
     */
    public static function castCTypeOrCData($data, array $args, Stub $stub) : array
    {
        if ($data instanceof CType) {
            $type = $data;
            $data = null;
        } else {
            $type = \FFI::typeof($data);
        }
        $stub->class = \sprintf('%s<%s> size %d align %d', \get_class($data ?? $type), $type->getName(), $type->getSize(), $type->getAlignment());
        return $type->getKind() === CType::TYPE_FLOAT || $type->getKind() === CType::TYPE_DOUBLE || $type->getKind() === (\defined('\\FFI\\CType::TYPE_LONGDOUBLE') ? CType::TYPE_LONGDOUBLE : -1) || $type->getKind() === CType::TYPE_UINT8 || $type->getKind() === CType::TYPE_SINT8 || $type->getKind() === CType::TYPE_UINT16 || $type->getKind() === CType::TYPE_SINT16 || $type->getKind() === CType::TYPE_UINT32 || $type->getKind() === CType::TYPE_SINT32 || $type->getKind() === CType::TYPE_UINT64 || $type->getKind() === CType::TYPE_SINT64 || $type->getKind() === CType::TYPE_BOOL || $type->getKind() === CType::TYPE_CHAR || $type->getKind() === CType::TYPE_ENUM ? null !== $data ? [Caster::PREFIX_VIRTUAL . 'cdata' => $data->cdata] : [] : ($type->getKind() === CType::TYPE_POINTER ? self::castFFIPointer($stub, $type, $data) : ($type->getKind() === CType::TYPE_STRUCT ? self::castFFIStructLike($type, $data) : ($type->getKind() === CType::TYPE_FUNC ? self::castFFIFunction($stub, $type) : $args)));
    }
    private static function castFFIFunction(Stub $stub, CType $type) : array
    {
        $arguments = [];
        for ($i = 0, $count = $type->getFuncParameterCount(); $i < $count; ++$i) {
            $param = $type->getFuncParameterType($i);
            $arguments[] = $param->getName();
        }
        $abi = $type->getFuncABI() === CType::ABI_DEFAULT || $type->getFuncABI() === CType::ABI_CDECL ? '[cdecl]' : ($type->getFuncABI() === CType::ABI_FASTCALL ? '[fastcall]' : ($type->getFuncABI() === CType::ABI_THISCALL ? '[thiscall]' : ($type->getFuncABI() === CType::ABI_STDCALL ? '[stdcall]' : ($type->getFuncABI() === CType::ABI_PASCAL ? '[pascal]' : ($type->getFuncABI() === CType::ABI_REGISTER ? '[register]' : ($type->getFuncABI() === CType::ABI_MS ? '[ms]' : ($type->getFuncABI() === CType::ABI_SYSV ? '[sysv]' : ($type->getFuncABI() === CType::ABI_VECTORCALL ? '[vectorcall]' : '[unknown abi]'))))))));
        $returnType = $type->getFuncReturnType();
        $stub->class = $abi . ' callable(' . \implode(', ', $arguments) . '): ' . $returnType->getName();
        return [Caster::PREFIX_VIRTUAL . 'returnType' => $returnType];
    }
    private static function castFFIPointer(Stub $stub, CType $type, CData $data = null) : array
    {
        $ptr = $type->getPointerType();
        if (null === $data) {
            return [Caster::PREFIX_VIRTUAL . '0' => $ptr];
        }
        return $ptr->getKind() === CType::TYPE_CHAR ? [Caster::PREFIX_VIRTUAL . 'cdata' => self::castFFIStringValue($data)] : ($ptr->getKind() === CType::TYPE_FUNC ? self::castFFIFunction($stub, $ptr) : [Caster::PREFIX_VIRTUAL . 'cdata' => $data[0]]);
    }
    /**
     * @return string|\Symfony\Component\VarDumper\Caster\CutStub
     */
    private static function castFFIStringValue(CData $data)
    {
        $result = [];
        for ($i = 0; $i < self::MAX_STRING_LENGTH; ++$i) {
            $result[$i] = $data[$i];
            if ("\x00" === $result[$i]) {
                return \implode('', $result);
            }
        }
        $string = \implode('', $result);
        $stub = new CutStub($string);
        $stub->cut = -1;
        $stub->value = $string;
        return $stub;
    }
    private static function castFFIStructLike(CType $type, CData $data = null) : array
    {
        $isUnion = ($type->getAttributes() & CType::ATTR_UNION) === CType::ATTR_UNION;
        $result = [];
        foreach ($type->getStructFieldNames() as $name) {
            $field = $type->getStructFieldType($name);
            // Retrieving the value of a field from a union containing
            // a pointer is not a safe operation, because may contain
            // incorrect data.
            $isUnsafe = $isUnion && CType::TYPE_POINTER === $field->getKind();
            if ($isUnsafe) {
                $result[Caster::PREFIX_VIRTUAL . $name . '?'] = $field;
            } elseif (null === $data) {
                $result[Caster::PREFIX_VIRTUAL . $name] = $field;
            } else {
                $fieldName = $data->{$name} instanceof CData ? '' : $field->getName() . ' ';
                $result[Caster::PREFIX_VIRTUAL . $fieldName . $name] = $data->{$name};
            }
        }
        return $result;
    }
}
