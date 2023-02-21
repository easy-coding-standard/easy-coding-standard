<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202302\Symfony\Component\VarDumper\Caster;

use FFI\CData;
use FFI\CType;
use ECSPrefix202302\Symfony\Component\VarDumper\Cloner\Stub;
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
        switch ($type->getKind()) {
            case CType::TYPE_FLOAT:
            case CType::TYPE_DOUBLE:
            case \defined('\\FFI\\CType::TYPE_LONGDOUBLE') ? CType::TYPE_LONGDOUBLE : -1:
            case CType::TYPE_UINT8:
            case CType::TYPE_SINT8:
            case CType::TYPE_UINT16:
            case CType::TYPE_SINT16:
            case CType::TYPE_UINT32:
            case CType::TYPE_SINT32:
            case CType::TYPE_UINT64:
            case CType::TYPE_SINT64:
            case CType::TYPE_BOOL:
            case CType::TYPE_CHAR:
            case CType::TYPE_ENUM:
                return null !== $data ? [Caster::PREFIX_VIRTUAL . 'cdata' => $data->cdata] : [];
            case CType::TYPE_POINTER:
                return self::castFFIPointer($stub, $type, $data);
            case CType::TYPE_STRUCT:
                return self::castFFIStructLike($type, $data);
            case CType::TYPE_FUNC:
                return self::castFFIFunction($stub, $type);
            default:
                return $args;
        }
    }
    private static function castFFIFunction(Stub $stub, CType $type) : array
    {
        $arguments = [];
        for ($i = 0, $count = $type->getFuncParameterCount(); $i < $count; ++$i) {
            $param = $type->getFuncParameterType($i);
            $arguments[] = $param->getName();
        }
        switch ($type->getFuncABI()) {
            case CType::ABI_DEFAULT:
            case CType::ABI_CDECL:
                $abi = '[cdecl]';
                break;
            case CType::ABI_FASTCALL:
                $abi = '[fastcall]';
                break;
            case CType::ABI_THISCALL:
                $abi = '[thiscall]';
                break;
            case CType::ABI_STDCALL:
                $abi = '[stdcall]';
                break;
            case CType::ABI_PASCAL:
                $abi = '[pascal]';
                break;
            case CType::ABI_REGISTER:
                $abi = '[register]';
                break;
            case CType::ABI_MS:
                $abi = '[ms]';
                break;
            case CType::ABI_SYSV:
                $abi = '[sysv]';
                break;
            case CType::ABI_VECTORCALL:
                $abi = '[vectorcall]';
                break;
            default:
                $abi = '[unknown abi]';
                break;
        }
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
        switch ($ptr->getKind()) {
            case CType::TYPE_CHAR:
                return [Caster::PREFIX_VIRTUAL . 'cdata' => self::castFFIStringValue($data)];
            case CType::TYPE_FUNC:
                return self::castFFIFunction($stub, $ptr);
            default:
                return [Caster::PREFIX_VIRTUAL . 'cdata' => $data[0]];
        }
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
