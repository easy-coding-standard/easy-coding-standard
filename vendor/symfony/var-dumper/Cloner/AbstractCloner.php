<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202305\Symfony\Component\VarDumper\Cloner;

use ECSPrefix202305\Symfony\Component\VarDumper\Caster\Caster;
use ECSPrefix202305\Symfony\Component\VarDumper\Exception\ThrowingCasterException;
/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements ClonerInterface
{
    public static $defaultCasters = ['__PHP_Incomplete_Class' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\Caster', 'castPhpIncompleteClass'], 'ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\CutStub' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castStub'], 'ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\CutArrayStub' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castCutArray'], 'ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ConstStub' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castStub'], 'ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\EnumStub' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castEnum'], 'Fiber' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\FiberCaster', 'castFiber'], 'Closure' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClosure'], 'Generator' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castGenerator'], 'ReflectionType' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castType'], 'ReflectionAttribute' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castAttribute'], 'ReflectionGenerator' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castReflectionGenerator'], 'ReflectionClass' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClass'], 'ReflectionClassConstant' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClassConstant'], 'ReflectionFunctionAbstract' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castFunctionAbstract'], 'ReflectionMethod' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castMethod'], 'ReflectionParameter' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castParameter'], 'ReflectionProperty' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castProperty'], 'ReflectionReference' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castReference'], 'ReflectionExtension' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castExtension'], 'ReflectionZendExtension' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castZendExtension'], 'ECSPrefix202305\\Doctrine\\Common\\Persistence\\ObjectManager' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix202305\\Doctrine\\Common\\Proxy\\Proxy' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castCommonProxy'], 'ECSPrefix202305\\Doctrine\\ORM\\Proxy\\Proxy' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castOrmProxy'], 'ECSPrefix202305\\Doctrine\\ORM\\PersistentCollection' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castPersistentCollection'], 'ECSPrefix202305\\Doctrine\\Persistence\\ObjectManager' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'DOMException' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castException'], 'DOMStringList' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNameList' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMImplementation' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castImplementation'], 'DOMImplementationList' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNode' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNode'], 'DOMNameSpaceNode' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNameSpaceNode'], 'DOMDocument' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDocument'], 'DOMNodeList' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNamedNodeMap' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMCharacterData' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castCharacterData'], 'DOMAttr' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castAttr'], 'DOMElement' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castElement'], 'DOMText' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castText'], 'DOMDocumentType' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDocumentType'], 'DOMNotation' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNotation'], 'DOMEntity' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castEntity'], 'DOMProcessingInstruction' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castProcessingInstruction'], 'DOMXPath' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castXPath'], 'XMLReader' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\XmlReaderCaster', 'castXmlReader'], 'ErrorException' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castErrorException'], 'Exception' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castException'], 'Error' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castError'], 'ECSPrefix202305\\Symfony\\Bridge\\Monolog\\Logger' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix202305\\Symfony\\Component\\DependencyInjection\\ContainerInterface' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix202305\\Symfony\\Component\\EventDispatcher\\EventDispatcherInterface' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix202305\\Symfony\\Component\\HttpClient\\AmpHttpClient' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'ECSPrefix202305\\Symfony\\Component\\HttpClient\\CurlHttpClient' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'ECSPrefix202305\\Symfony\\Component\\HttpClient\\NativeHttpClient' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'ECSPrefix202305\\Symfony\\Component\\HttpClient\\Response\\AmpResponse' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'ECSPrefix202305\\Symfony\\Component\\HttpClient\\Response\\CurlResponse' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'ECSPrefix202305\\Symfony\\Component\\HttpClient\\Response\\NativeResponse' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'ECSPrefix202305\\Symfony\\Component\\HttpFoundation\\Request' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castRequest'], 'ECSPrefix202305\\Symfony\\Component\\Uid\\Ulid' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castUlid'], 'ECSPrefix202305\\Symfony\\Component\\Uid\\Uuid' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castUuid'], 'ECSPrefix202305\\Symfony\\Component\\VarExporter\\Internal\\LazyObjectState' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castLazyObjectState'], 'ECSPrefix202305\\Symfony\\Component\\VarDumper\\Exception\\ThrowingCasterException' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castThrowingCasterException'], 'ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\TraceStub' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castTraceStub'], 'ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\FrameStub' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castFrameStub'], 'ECSPrefix202305\\Symfony\\Component\\VarDumper\\Cloner\\AbstractCloner' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix202305\\Symfony\\Component\\ErrorHandler\\Exception\\SilencedErrorContext' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castSilencedErrorContext'], 'ECSPrefix202305\\Imagine\\Image\\ImageInterface' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ImagineCaster', 'castImage'], 'ECSPrefix202305\\Ramsey\\Uuid\\UuidInterface' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\UuidCaster', 'castRamseyUuid'], 'ECSPrefix202305\\ProxyManager\\Proxy\\ProxyInterface' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ProxyManagerCaster', 'castProxy'], 'PHPUnit_Framework_MockObject_MockObject' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix202305\\PHPUnit\\Framework\\MockObject\\MockObject' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix202305\\PHPUnit\\Framework\\MockObject\\Stub' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix202305\\Prophecy\\Prophecy\\ProphecySubjectInterface' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ECSPrefix202305\\Mockery\\MockInterface' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PDO' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\PdoCaster', 'castPdo'], 'PDOStatement' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\PdoCaster', 'castPdoStatement'], 'AMQPConnection' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castConnection'], 'AMQPChannel' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castChannel'], 'AMQPQueue' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castQueue'], 'AMQPExchange' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castExchange'], 'AMQPEnvelope' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castEnvelope'], 'ArrayObject' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castArrayObject'], 'ArrayIterator' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castArrayIterator'], 'SplDoublyLinkedList' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castDoublyLinkedList'], 'SplFileInfo' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castFileInfo'], 'SplFileObject' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castFileObject'], 'SplHeap' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castHeap'], 'SplObjectStorage' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castObjectStorage'], 'SplPriorityQueue' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castHeap'], 'OuterIterator' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castOuterIterator'], 'WeakReference' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castWeakReference'], 'Redis' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedis'], 'RedisArray' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedisArray'], 'RedisCluster' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedisCluster'], 'DateTimeInterface' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castDateTime'], 'DateInterval' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castInterval'], 'DateTimeZone' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castTimeZone'], 'DatePeriod' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castPeriod'], 'GMP' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\GmpCaster', 'castGmp'], 'MessageFormatter' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castMessageFormatter'], 'NumberFormatter' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castNumberFormatter'], 'IntlTimeZone' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlTimeZone'], 'IntlCalendar' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlCalendar'], 'IntlDateFormatter' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlDateFormatter'], 'Memcached' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\MemcachedCaster', 'castMemcached'], 'ECSPrefix202305\\Ds\\Collection' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castCollection'], 'ECSPrefix202305\\Ds\\Map' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castMap'], 'ECSPrefix202305\\Ds\\Pair' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castPair'], 'ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DsPairStub' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castPairStub'], 'mysqli_driver' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\MysqliCaster', 'castMysqliDriver'], 'CurlHandle' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castCurl'], ':dba' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castDba'], ':dba persistent' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castDba'], 'GdImage' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castGd'], ':gd' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castGd'], ':pgsql large object' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLargeObject'], ':pgsql link' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLink'], ':pgsql link persistent' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLink'], ':pgsql result' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castResult'], ':process' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castProcess'], ':stream' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStream'], 'OpenSSLCertificate' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castOpensslX509'], ':OpenSSL X.509' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castOpensslX509'], ':persistent stream' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStream'], ':stream-context' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStreamContext'], 'XmlParser' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\XmlResourceCaster', 'castXml'], ':xml' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\XmlResourceCaster', 'castXml'], 'RdKafka' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castRdKafka'], 'ECSPrefix202305\\RdKafka\\Conf' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castConf'], 'ECSPrefix202305\\RdKafka\\KafkaConsumer' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castKafkaConsumer'], 'ECSPrefix202305\\RdKafka\\Metadata\\Broker' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castBrokerMetadata'], 'ECSPrefix202305\\RdKafka\\Metadata\\Collection' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castCollectionMetadata'], 'ECSPrefix202305\\RdKafka\\Metadata\\Partition' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castPartitionMetadata'], 'ECSPrefix202305\\RdKafka\\Metadata\\Topic' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicMetadata'], 'ECSPrefix202305\\RdKafka\\Message' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castMessage'], 'ECSPrefix202305\\RdKafka\\Topic' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopic'], 'ECSPrefix202305\\RdKafka\\TopicPartition' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicPartition'], 'ECSPrefix202305\\RdKafka\\TopicConf' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicConf'], 'ECSPrefix202305\\FFI\\CData' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\FFICaster', 'castCTypeOrCData'], 'ECSPrefix202305\\FFI\\CType' => ['ECSPrefix202305\\Symfony\\Component\\VarDumper\\Caster\\FFICaster', 'castCTypeOrCData']];
    protected $maxItems = 2500;
    protected $maxString = -1;
    protected $minDepth = 1;
    /**
     * @var array<string, list<callable>>
     */
    private $casters = [];
    /**
     * @var callable|null
     */
    private $prevErrorHandler;
    /**
     * @var mixed[]
     */
    private $classInfo = [];
    /**
     * @var int
     */
    private $filter = 0;
    /**
     * @param callable[]|null $casters A map of casters
     *
     * @see addCasters
     */
    public function __construct(array $casters = null)
    {
        $this->addCasters($casters ?? static::$defaultCasters);
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
    public function addCasters(array $casters)
    {
        foreach ($casters as $type => $callback) {
            $this->casters[$type][] = $callback;
        }
    }
    /**
     * Sets the maximum number of items to clone past the minimum depth in nested structures.
     */
    public function setMaxItems(int $maxItems)
    {
        $this->maxItems = $maxItems;
    }
    /**
     * Sets the maximum cloned length for strings.
     */
    public function setMaxString(int $maxString)
    {
        $this->maxString = $maxString;
    }
    /**
     * Sets the minimum tree depth where we are guaranteed to clone all the items.  After this
     * depth is reached, only setMaxItems items will be cloned.
     */
    public function setMinDepth(int $minDepth)
    {
        $this->minDepth = $minDepth;
    }
    /**
     * Clones a PHP variable.
     *
     * @param int $filter A bit field of Caster::EXCLUDE_* constants
     * @param mixed $var
     */
    public function cloneVar($var, int $filter = 0) : Data
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
            return new Data($this->doClone($var));
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
     * @param mixed $var
     */
    protected abstract function doClone($var) : array;
    /**
     * Casts an object to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     */
    protected function castObject(Stub $stub, bool $isNested) : array
    {
        $obj = $stub->value;
        $class = $stub->class;
        if (\strpos($class, "@anonymous\x00") !== \false) {
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
            $fileInfo = $r->isInternal() || $r->isSubclassOf(Stub::class) ? [] : ['file' => $r->getFileName(), 'line' => $r->getStartLine()];
            $this->classInfo[$class] = [$i, $parents, $hasDebugInfo, $fileInfo];
        }
        $stub->attr += $fileInfo;
        $a = Caster::castObject($obj, $class, $hasDebugInfo, $stub->class);
        try {
            while ($i--) {
                if (!empty($this->casters[$p = $parents[$i]])) {
                    foreach ($this->casters[$p] as $callback) {
                        $a = $callback($obj, $a, $stub, $isNested, $this->filter);
                    }
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '') . '⚠' => new ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
    /**
     * Casts a resource to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     */
    protected function castResource(Stub $stub, bool $isNested) : array
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
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '') . '⚠' => new ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
}
