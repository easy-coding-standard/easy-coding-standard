<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\VarDumper\Cloner;

use ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster;
use ECSPrefix20211002\Symfony\Component\VarDumper\Exception\ThrowingCasterException;
/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements \ECSPrefix20211002\Symfony\Component\VarDumper\Cloner\ClonerInterface
{
    public static $defaultCasters = ['__PHP_Incomplete_Class' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\Caster', 'castPhpIncompleteClass'], 'ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\CutStub' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castStub'], 'ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\CutArrayStub' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castCutArray'], 'ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ConstStub' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castStub'], 'ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\EnumStub' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castEnum'], 'Closure' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClosure'], 'Generator' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castGenerator'], 'ReflectionType' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castType'], 'ReflectionAttribute' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castAttribute'], 'ReflectionGenerator' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castReflectionGenerator'], 'ReflectionClass' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClass'], 'ReflectionClassConstant' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClassConstant'], 'ReflectionFunctionAbstract' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castFunctionAbstract'], 'ReflectionMethod' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castMethod'], 'ReflectionParameter' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castParameter'], 'ReflectionProperty' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castProperty'], 'ReflectionReference' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castReference'], 'ReflectionExtension' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castExtension'], 'ReflectionZendExtension' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castZendExtension'], 'ECSPrefix20211002\\Doctrine\\Common\\Persistence\\ObjectManager' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix20211002\\Doctrine\\Common\\Proxy\\Proxy' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castCommonProxy'], 'ECSPrefix20211002\\Doctrine\\ORM\\Proxy\\Proxy' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castOrmProxy'], 'ECSPrefix20211002\\Doctrine\\ORM\\PersistentCollection' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castPersistentCollection'], 'ECSPrefix20211002\\Doctrine\\Persistence\\ObjectManager' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'DOMException' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castException'], 'DOMStringList' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNameList' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMImplementation' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castImplementation'], 'DOMImplementationList' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNode' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNode'], 'DOMNameSpaceNode' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNameSpaceNode'], 'DOMDocument' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDocument'], 'DOMNodeList' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNamedNodeMap' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMCharacterData' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castCharacterData'], 'DOMAttr' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castAttr'], 'DOMElement' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castElement'], 'DOMText' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castText'], 'DOMTypeinfo' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castTypeinfo'], 'DOMDomError' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDomError'], 'DOMLocator' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLocator'], 'DOMDocumentType' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDocumentType'], 'DOMNotation' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNotation'], 'DOMEntity' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castEntity'], 'DOMProcessingInstruction' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castProcessingInstruction'], 'DOMXPath' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castXPath'], 'XMLReader' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\XmlReaderCaster', 'castXmlReader'], 'ErrorException' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castErrorException'], 'Exception' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castException'], 'Error' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castError'], 'ECSPrefix20211002\\Symfony\\Bridge\\Monolog\\Logger' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix20211002\\Symfony\\Component\\DependencyInjection\\ContainerInterface' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix20211002\\Symfony\\Component\\EventDispatcher\\EventDispatcherInterface' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix20211002\\Symfony\\Component\\HttpClient\\CurlHttpClient' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'ECSPrefix20211002\\Symfony\\Component\\HttpClient\\NativeHttpClient' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'ECSPrefix20211002\\Symfony\\Component\\HttpClient\\Response\\CurlResponse' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'ECSPrefix20211002\\Symfony\\Component\\HttpClient\\Response\\NativeResponse' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'ECSPrefix20211002\\Symfony\\Component\\HttpFoundation\\Request' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castRequest'], 'ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Exception\\ThrowingCasterException' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castThrowingCasterException'], 'ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\TraceStub' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castTraceStub'], 'ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\FrameStub' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castFrameStub'], 'ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Cloner\\AbstractCloner' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix20211002\\Symfony\\Component\\ErrorHandler\\Exception\\SilencedErrorContext' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castSilencedErrorContext'], 'ECSPrefix20211002\\Imagine\\Image\\ImageInterface' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ImagineCaster', 'castImage'], 'ECSPrefix20211002\\Ramsey\\Uuid\\UuidInterface' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\UuidCaster', 'castRamseyUuid'], 'ECSPrefix20211002\\ProxyManager\\Proxy\\ProxyInterface' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ProxyManagerCaster', 'castProxy'], 'PHPUnit_Framework_MockObject_MockObject' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix20211002\\PHPUnit\\Framework\\MockObject\\MockObject' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix20211002\\PHPUnit\\Framework\\MockObject\\Stub' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix20211002\\Prophecy\\Prophecy\\ProphecySubjectInterface' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix20211002\\Mockery\\MockInterface' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PDO' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\PdoCaster', 'castPdo'], 'PDOStatement' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\PdoCaster', 'castPdoStatement'], 'AMQPConnection' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castConnection'], 'AMQPChannel' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castChannel'], 'AMQPQueue' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castQueue'], 'AMQPExchange' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castExchange'], 'AMQPEnvelope' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castEnvelope'], 'ArrayObject' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castArrayObject'], 'ArrayIterator' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castArrayIterator'], 'SplDoublyLinkedList' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castDoublyLinkedList'], 'SplFileInfo' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castFileInfo'], 'SplFileObject' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castFileObject'], 'SplHeap' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castHeap'], 'SplObjectStorage' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castObjectStorage'], 'SplPriorityQueue' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castHeap'], 'OuterIterator' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castOuterIterator'], 'WeakReference' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castWeakReference'], 'Redis' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedis'], 'RedisArray' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedisArray'], 'RedisCluster' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedisCluster'], 'DateTimeInterface' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castDateTime'], 'DateInterval' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castInterval'], 'DateTimeZone' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castTimeZone'], 'DatePeriod' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castPeriod'], 'GMP' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\GmpCaster', 'castGmp'], 'MessageFormatter' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castMessageFormatter'], 'NumberFormatter' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castNumberFormatter'], 'IntlTimeZone' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlTimeZone'], 'IntlCalendar' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlCalendar'], 'IntlDateFormatter' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlDateFormatter'], 'Memcached' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\MemcachedCaster', 'castMemcached'], 'ECSPrefix20211002\\Ds\\Collection' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castCollection'], 'ECSPrefix20211002\\Ds\\Map' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castMap'], 'ECSPrefix20211002\\Ds\\Pair' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castPair'], 'ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DsPairStub' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castPairStub'], 'CurlHandle' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castCurl'], ':curl' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castCurl'], ':dba' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castDba'], ':dba persistent' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castDba'], 'GdImage' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castGd'], ':gd' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castGd'], ':mysql link' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castMysqlLink'], ':pgsql large object' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLargeObject'], ':pgsql link' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLink'], ':pgsql link persistent' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLink'], ':pgsql result' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castResult'], ':process' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castProcess'], ':stream' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStream'], 'OpenSSLCertificate' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castOpensslX509'], ':OpenSSL X.509' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castOpensslX509'], ':persistent stream' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStream'], ':stream-context' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStreamContext'], 'XmlParser' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\XmlResourceCaster', 'castXml'], ':xml' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\XmlResourceCaster', 'castXml'], 'RdKafka' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castRdKafka'], 'ECSPrefix20211002\\RdKafka\\Conf' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castConf'], 'ECSPrefix20211002\\RdKafka\\KafkaConsumer' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castKafkaConsumer'], 'ECSPrefix20211002\\RdKafka\\Metadata\\Broker' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castBrokerMetadata'], 'ECSPrefix20211002\\RdKafka\\Metadata\\Collection' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castCollectionMetadata'], 'ECSPrefix20211002\\RdKafka\\Metadata\\Partition' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castPartitionMetadata'], 'ECSPrefix20211002\\RdKafka\\Metadata\\Topic' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicMetadata'], 'ECSPrefix20211002\\RdKafka\\Message' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castMessage'], 'ECSPrefix20211002\\RdKafka\\Topic' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopic'], 'ECSPrefix20211002\\RdKafka\\TopicPartition' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicPartition'], 'ECSPrefix20211002\\RdKafka\\TopicConf' => ['ECSPrefix20211002\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicConf']];
    protected $maxItems = 2500;
    protected $maxString = -1;
    protected $minDepth = 1;
    private $casters = [];
    private $prevErrorHandler;
    private $classInfo = [];
    private $filter = 0;
    /**
     * @param callable[]|null $casters A map of casters
     *
     * @see addCasters
     */
    public function __construct(array $casters = null)
    {
        if (null === $casters) {
            $casters = static::$defaultCasters;
        }
        $this->addCasters($casters);
    }
    /**
     * Adds casters for resources and objects.
     *
     * Maps resources or objects types to a callback.
     * Types are in the key, with a callable caster for value.
     * Resource types are to be prefixed with a `:`,
     * see e.g. static::$defaultCasters.
     *
     * @param callable[] $casters A map of casters
     */
    public function addCasters($casters)
    {
        foreach ($casters as $type => $callback) {
            $this->casters[$type][] = $callback;
        }
    }
    /**
     * Sets the maximum number of items to clone past the minimum depth in nested structures.
     * @param int $maxItems
     */
    public function setMaxItems($maxItems)
    {
        $this->maxItems = $maxItems;
    }
    /**
     * Sets the maximum cloned length for strings.
     * @param int $maxString
     */
    public function setMaxString($maxString)
    {
        $this->maxString = $maxString;
    }
    /**
     * Sets the minimum tree depth where we are guaranteed to clone all the items.  After this
     * depth is reached, only setMaxItems items will be cloned.
     * @param int $minDepth
     */
    public function setMinDepth($minDepth)
    {
        $this->minDepth = $minDepth;
    }
    /**
     * Clones a PHP variable.
     *
     * @param mixed $var    Any PHP variable
     * @param int   $filter A bit field of Caster::EXCLUDE_* constants
     *
     * @return Data The cloned variable represented by a Data object
     */
    public function cloneVar($var, $filter = 0)
    {
        $this->prevErrorHandler = \set_error_handler(function ($type, $msg, $file, $line, $context = []) {
            if (\E_RECOVERABLE_ERROR === $type || \E_USER_ERROR === $type) {
                // Cloner never dies
                throw new \ErrorException($msg, 0, $type, $file, $line);
            }
            if ($this->prevErrorHandler) {
                return ($this->prevErrorHandler)($type, $msg, $file, $line, $context);
            }
            return \false;
        });
        $this->filter = $filter;
        if ($gc = \gc_enabled()) {
            \gc_disable();
        }
        try {
            return new \ECSPrefix20211002\Symfony\Component\VarDumper\Cloner\Data($this->doClone($var));
        } finally {
            if ($gc) {
                \gc_enable();
            }
            \restore_error_handler();
            $this->prevErrorHandler = null;
        }
    }
    /**
     * Effectively clones the PHP variable.
     *
     * @param mixed $var Any PHP variable
     *
     * @return array The cloned variable represented in an array
     */
    protected abstract function doClone($var);
    /**
     * Casts an object to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     *
     * @return array The object casted as array
     * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
     */
    protected function castObject($stub, $isNested)
    {
        $obj = $stub->value;
        $class = $stub->class;
        if (\PHP_VERSION_ID < 80000 ? "\0" === ($class[15] ?? null) : \strpos($class, "@anonymous\0") !== \false) {
            $stub->class = \get_debug_type($obj);
        }
        if (isset($this->classInfo[$class])) {
            [$i, $parents, $hasDebugInfo, $fileInfo] = $this->classInfo[$class];
        } else {
            $i = 2;
            $parents = [$class];
            $hasDebugInfo = \method_exists($class, '__debugInfo');
            foreach (\class_parents($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            foreach (\class_implements($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            $parents[] = '*';
            $r = new \ReflectionClass($class);
            $fileInfo = $r->isInternal() || $r->isSubclassOf(\ECSPrefix20211002\Symfony\Component\VarDumper\Cloner\Stub::class) ? [] : ['file' => $r->getFileName(), 'line' => $r->getStartLine()];
            $this->classInfo[$class] = [$i, $parents, $hasDebugInfo, $fileInfo];
        }
        $stub->attr += $fileInfo;
        $a = \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::castObject($obj, $class, $hasDebugInfo, $stub->class);
        try {
            while ($i--) {
                if (!empty($this->casters[$p = $parents[$i]])) {
                    foreach ($this->casters[$p] as $callback) {
                        $a = $callback($obj, $a, $stub, $isNested, $this->filter);
                    }
                }
            }
        } catch (\Exception $e) {
            $a = [(\ECSPrefix20211002\Symfony\Component\VarDumper\Cloner\Stub::TYPE_OBJECT === $stub->type ? \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL : '') . '⚠' => new \ECSPrefix20211002\Symfony\Component\VarDumper\Exception\ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
    /**
     * Casts a resource to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     *
     * @return array The resource casted as array
     * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
     */
    protected function castResource($stub, $isNested)
    {
        $a = [];
        $res = $stub->value;
        $type = $stub->class;
        try {
            if (!empty($this->casters[':' . $type])) {
                foreach ($this->casters[':' . $type] as $callback) {
                    $a = $callback($res, $a, $stub, $isNested, $this->filter);
                }
            }
        } catch (\Exception $e) {
            $a = [(\ECSPrefix20211002\Symfony\Component\VarDumper\Cloner\Stub::TYPE_OBJECT === $stub->type ? \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL : '') . '⚠' => new \ECSPrefix20211002\Symfony\Component\VarDumper\Exception\ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
}
