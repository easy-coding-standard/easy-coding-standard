<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\VarDumper\Caster;

use ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub;
/**
 * Casts DOM related classes to array representation.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @final
 */
class DOMCaster
{
    const ERROR_CODES = [\DOM_PHP_ERR => 'DOM_PHP_ERR', \DOM_INDEX_SIZE_ERR => 'DOM_INDEX_SIZE_ERR', \DOMSTRING_SIZE_ERR => 'DOMSTRING_SIZE_ERR', \DOM_HIERARCHY_REQUEST_ERR => 'DOM_HIERARCHY_REQUEST_ERR', \DOM_WRONG_DOCUMENT_ERR => 'DOM_WRONG_DOCUMENT_ERR', \DOM_INVALID_CHARACTER_ERR => 'DOM_INVALID_CHARACTER_ERR', \DOM_NO_DATA_ALLOWED_ERR => 'DOM_NO_DATA_ALLOWED_ERR', \DOM_NO_MODIFICATION_ALLOWED_ERR => 'DOM_NO_MODIFICATION_ALLOWED_ERR', \DOM_NOT_FOUND_ERR => 'DOM_NOT_FOUND_ERR', \DOM_NOT_SUPPORTED_ERR => 'DOM_NOT_SUPPORTED_ERR', \DOM_INUSE_ATTRIBUTE_ERR => 'DOM_INUSE_ATTRIBUTE_ERR', \DOM_INVALID_STATE_ERR => 'DOM_INVALID_STATE_ERR', \DOM_SYNTAX_ERR => 'DOM_SYNTAX_ERR', \DOM_INVALID_MODIFICATION_ERR => 'DOM_INVALID_MODIFICATION_ERR', \DOM_NAMESPACE_ERR => 'DOM_NAMESPACE_ERR', \DOM_INVALID_ACCESS_ERR => 'DOM_INVALID_ACCESS_ERR', \DOM_VALIDATION_ERR => 'DOM_VALIDATION_ERR'];
    const NODE_TYPES = [\XML_ELEMENT_NODE => 'XML_ELEMENT_NODE', \XML_ATTRIBUTE_NODE => 'XML_ATTRIBUTE_NODE', \XML_TEXT_NODE => 'XML_TEXT_NODE', \XML_CDATA_SECTION_NODE => 'XML_CDATA_SECTION_NODE', \XML_ENTITY_REF_NODE => 'XML_ENTITY_REF_NODE', \XML_ENTITY_NODE => 'XML_ENTITY_NODE', \XML_PI_NODE => 'XML_PI_NODE', \XML_COMMENT_NODE => 'XML_COMMENT_NODE', \XML_DOCUMENT_NODE => 'XML_DOCUMENT_NODE', \XML_DOCUMENT_TYPE_NODE => 'XML_DOCUMENT_TYPE_NODE', \XML_DOCUMENT_FRAG_NODE => 'XML_DOCUMENT_FRAG_NODE', \XML_NOTATION_NODE => 'XML_NOTATION_NODE', \XML_HTML_DOCUMENT_NODE => 'XML_HTML_DOCUMENT_NODE', \XML_DTD_NODE => 'XML_DTD_NODE', \XML_ELEMENT_DECL_NODE => 'XML_ELEMENT_DECL_NODE', \XML_ATTRIBUTE_DECL_NODE => 'XML_ATTRIBUTE_DECL_NODE', \XML_ENTITY_DECL_NODE => 'XML_ENTITY_DECL_NODE', \XML_NAMESPACE_DECL_NODE => 'XML_NAMESPACE_DECL_NODE'];
    /**
     * @param \DOMException $e
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castException($e, array $a, $stub, $isNested)
    {
        $k = \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_PROTECTED . 'code';
        if (isset($a[$k], self::ERROR_CODES[$a[$k]])) {
            $a[$k] = new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ConstStub(self::ERROR_CODES[$a[$k]], $a[$k]);
        }
        return $a;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castLength($dom, array $a, $stub, $isNested)
    {
        $a += ['length' => $dom->length];
        return $a;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castImplementation($dom, array $a, $stub, $isNested)
    {
        $a += [\ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'Core' => '1.0', \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'XML' => '2.0'];
        return $a;
    }
    /**
     * @param \DOMNode $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castNode($dom, array $a, $stub, $isNested)
    {
        $a += ['nodeName' => $dom->nodeName, 'nodeValue' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\CutStub($dom->nodeValue), 'nodeType' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ConstStub(self::NODE_TYPES[$dom->nodeType], $dom->nodeType), 'parentNode' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\CutStub($dom->parentNode), 'childNodes' => $dom->childNodes, 'firstChild' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\CutStub($dom->firstChild), 'lastChild' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\CutStub($dom->lastChild), 'previousSibling' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\CutStub($dom->previousSibling), 'nextSibling' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\CutStub($dom->nextSibling), 'attributes' => $dom->attributes, 'ownerDocument' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\CutStub($dom->ownerDocument), 'namespaceURI' => $dom->namespaceURI, 'prefix' => $dom->prefix, 'localName' => $dom->localName, 'baseURI' => $dom->baseURI ? new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\LinkStub($dom->baseURI) : $dom->baseURI, 'textContent' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\CutStub($dom->textContent)];
        return $a;
    }
    /**
     * @param \DOMNameSpaceNode $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castNameSpaceNode($dom, array $a, $stub, $isNested)
    {
        $a += ['nodeName' => $dom->nodeName, 'nodeValue' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\CutStub($dom->nodeValue), 'nodeType' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ConstStub(self::NODE_TYPES[$dom->nodeType], $dom->nodeType), 'prefix' => $dom->prefix, 'localName' => $dom->localName, 'namespaceURI' => $dom->namespaceURI, 'ownerDocument' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\CutStub($dom->ownerDocument), 'parentNode' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\CutStub($dom->parentNode)];
        return $a;
    }
    /**
     * @param \DOMDocument $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     * @param int $filter
     */
    public static function castDocument($dom, array $a, $stub, $isNested, $filter = 0)
    {
        $a += ['doctype' => $dom->doctype, 'implementation' => $dom->implementation, 'documentElement' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\CutStub($dom->documentElement), 'actualEncoding' => $dom->actualEncoding, 'encoding' => $dom->encoding, 'xmlEncoding' => $dom->xmlEncoding, 'standalone' => $dom->standalone, 'xmlStandalone' => $dom->xmlStandalone, 'version' => $dom->version, 'xmlVersion' => $dom->xmlVersion, 'strictErrorChecking' => $dom->strictErrorChecking, 'documentURI' => $dom->documentURI ? new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\LinkStub($dom->documentURI) : $dom->documentURI, 'config' => $dom->config, 'formatOutput' => $dom->formatOutput, 'validateOnParse' => $dom->validateOnParse, 'resolveExternals' => $dom->resolveExternals, 'preserveWhiteSpace' => $dom->preserveWhiteSpace, 'recover' => $dom->recover, 'substituteEntities' => $dom->substituteEntities];
        if (!($filter & \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::EXCLUDE_VERBOSE)) {
            $formatOutput = $dom->formatOutput;
            $dom->formatOutput = \true;
            $a += [\ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'xml' => $dom->saveXML()];
            $dom->formatOutput = $formatOutput;
        }
        return $a;
    }
    /**
     * @param \DOMCharacterData $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castCharacterData($dom, array $a, $stub, $isNested)
    {
        $a += ['data' => $dom->data, 'length' => $dom->length];
        return $a;
    }
    /**
     * @param \DOMAttr $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castAttr($dom, array $a, $stub, $isNested)
    {
        $a += ['name' => $dom->name, 'specified' => $dom->specified, 'value' => $dom->value, 'ownerElement' => $dom->ownerElement, 'schemaTypeInfo' => $dom->schemaTypeInfo];
        return $a;
    }
    /**
     * @param \DOMElement $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castElement($dom, array $a, $stub, $isNested)
    {
        $a += ['tagName' => $dom->tagName, 'schemaTypeInfo' => $dom->schemaTypeInfo];
        return $a;
    }
    /**
     * @param \DOMText $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castText($dom, array $a, $stub, $isNested)
    {
        $a += ['wholeText' => $dom->wholeText];
        return $a;
    }
    /**
     * @param \DOMTypeinfo $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castTypeinfo($dom, array $a, $stub, $isNested)
    {
        $a += ['typeName' => $dom->typeName, 'typeNamespace' => $dom->typeNamespace];
        return $a;
    }
    /**
     * @param \DOMDomError $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castDomError($dom, array $a, $stub, $isNested)
    {
        $a += ['severity' => $dom->severity, 'message' => $dom->message, 'type' => $dom->type, 'relatedException' => $dom->relatedException, 'related_data' => $dom->related_data, 'location' => $dom->location];
        return $a;
    }
    /**
     * @param \DOMLocator $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castLocator($dom, array $a, $stub, $isNested)
    {
        $a += ['lineNumber' => $dom->lineNumber, 'columnNumber' => $dom->columnNumber, 'offset' => $dom->offset, 'relatedNode' => $dom->relatedNode, 'uri' => $dom->uri ? new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\LinkStub($dom->uri, $dom->lineNumber) : $dom->uri];
        return $a;
    }
    /**
     * @param \DOMDocumentType $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castDocumentType($dom, array $a, $stub, $isNested)
    {
        $a += ['name' => $dom->name, 'entities' => $dom->entities, 'notations' => $dom->notations, 'publicId' => $dom->publicId, 'systemId' => $dom->systemId, 'internalSubset' => $dom->internalSubset];
        return $a;
    }
    /**
     * @param \DOMNotation $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castNotation($dom, array $a, $stub, $isNested)
    {
        $a += ['publicId' => $dom->publicId, 'systemId' => $dom->systemId];
        return $a;
    }
    /**
     * @param \DOMEntity $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castEntity($dom, array $a, $stub, $isNested)
    {
        $a += ['publicId' => $dom->publicId, 'systemId' => $dom->systemId, 'notationName' => $dom->notationName, 'actualEncoding' => $dom->actualEncoding, 'encoding' => $dom->encoding, 'version' => $dom->version];
        return $a;
    }
    /**
     * @param \DOMProcessingInstruction $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castProcessingInstruction($dom, array $a, $stub, $isNested)
    {
        $a += ['target' => $dom->target, 'data' => $dom->data];
        return $a;
    }
    /**
     * @param \DOMXPath $dom
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castXPath($dom, array $a, $stub, $isNested)
    {
        $a += ['document' => $dom->document];
        return $a;
    }
}
