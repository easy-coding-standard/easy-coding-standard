<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ConfigTransformer20210601\Symfony\Component\VarDumper\Cloner;

use ConfigTransformer20210601\Symfony\Component\VarDumper\Caster\Caster;
use ConfigTransformer20210601\Symfony\Component\VarDumper\Exception\ThrowingCasterException;
/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements \ConfigTransformer20210601\Symfony\Component\VarDumper\Cloner\ClonerInterface
{
    public static $defaultCasters = ['__PHP_Incomplete_Class' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\Caster', 'castPhpIncompleteClass'], 'ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\CutStub' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castStub'], 'ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\CutArrayStub' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castCutArray'], 'ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ConstStub' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castStub'], 'ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\EnumStub' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'castEnum'], 'Closure' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClosure'], 'Generator' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castGenerator'], 'ReflectionType' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castType'], 'ReflectionAttribute' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castAttribute'], 'ReflectionGenerator' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castReflectionGenerator'], 'ReflectionClass' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClass'], 'ReflectionClassConstant' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castClassConstant'], 'ReflectionFunctionAbstract' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castFunctionAbstract'], 'ReflectionMethod' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castMethod'], 'ReflectionParameter' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castParameter'], 'ReflectionProperty' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castProperty'], 'ReflectionReference' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castReference'], 'ReflectionExtension' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castExtension'], 'ReflectionZendExtension' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ReflectionCaster', 'castZendExtension'], 'ConfigTransformer20210601\\Doctrine\\Common\\Persistence\\ObjectManager' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ConfigTransformer20210601\\Doctrine\\Common\\Proxy\\Proxy' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castCommonProxy'], 'ConfigTransformer20210601\\Doctrine\\ORM\\Proxy\\Proxy' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castOrmProxy'], 'ConfigTransformer20210601\\Doctrine\\ORM\\PersistentCollection' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DoctrineCaster', 'castPersistentCollection'], 'ConfigTransformer20210601\\Doctrine\\Persistence\\ObjectManager' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'DOMException' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castException'], 'DOMStringList' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNameList' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMImplementation' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castImplementation'], 'DOMImplementationList' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNode' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNode'], 'DOMNameSpaceNode' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNameSpaceNode'], 'DOMDocument' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDocument'], 'DOMNodeList' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMNamedNodeMap' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLength'], 'DOMCharacterData' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castCharacterData'], 'DOMAttr' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castAttr'], 'DOMElement' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castElement'], 'DOMText' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castText'], 'DOMTypeinfo' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castTypeinfo'], 'DOMDomError' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDomError'], 'DOMLocator' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castLocator'], 'DOMDocumentType' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castDocumentType'], 'DOMNotation' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castNotation'], 'DOMEntity' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castEntity'], 'DOMProcessingInstruction' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castProcessingInstruction'], 'DOMXPath' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DOMCaster', 'castXPath'], 'XMLReader' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\XmlReaderCaster', 'castXmlReader'], 'ErrorException' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castErrorException'], 'Exception' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castException'], 'Error' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castError'], 'ConfigTransformer20210601\\Symfony\\Bridge\\Monolog\\Logger' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ConfigTransformer20210601\\Symfony\\Component\\DependencyInjection\\ContainerInterface' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ConfigTransformer20210601\\Symfony\\Component\\EventDispatcher\\EventDispatcherInterface' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ConfigTransformer20210601\\Symfony\\Component\\HttpClient\\CurlHttpClient' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'ConfigTransformer20210601\\Symfony\\Component\\HttpClient\\NativeHttpClient' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClient'], 'ConfigTransformer20210601\\Symfony\\Component\\HttpClient\\Response\\CurlResponse' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'ConfigTransformer20210601\\Symfony\\Component\\HttpClient\\Response\\NativeResponse' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castHttpClientResponse'], 'ConfigTransformer20210601\\Symfony\\Component\\HttpFoundation\\Request' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SymfonyCaster', 'castRequest'], 'ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Exception\\ThrowingCasterException' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castThrowingCasterException'], 'ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\TraceStub' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castTraceStub'], 'ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\FrameStub' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castFrameStub'], 'ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Cloner\\AbstractCloner' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ConfigTransformer20210601\\Symfony\\Component\\ErrorHandler\\Exception\\SilencedErrorContext' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ExceptionCaster', 'castSilencedErrorContext'], 'ConfigTransformer20210601\\Imagine\\Image\\ImageInterface' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ImagineCaster', 'castImage'], 'ConfigTransformer20210601\\Ramsey\\Uuid\\UuidInterface' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\UuidCaster', 'castRamseyUuid'], 'ConfigTransformer20210601\\ProxyManager\\Proxy\\ProxyInterface' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ProxyManagerCaster', 'castProxy'], 'PHPUnit_Framework_MockObject_MockObject' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ConfigTransformer20210601\\PHPUnit\\Framework\\MockObject\\MockObject' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ConfigTransformer20210601\\PHPUnit\\Framework\\MockObject\\Stub' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ConfigTransformer20210601\\Prophecy\\Prophecy\\ProphecySubjectInterface' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'ConfigTransformer20210601\\Mockery\\MockInterface' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\StubCaster', 'cutInternals'], 'PDO' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\PdoCaster', 'castPdo'], 'PDOStatement' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\PdoCaster', 'castPdoStatement'], 'AMQPConnection' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castConnection'], 'AMQPChannel' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castChannel'], 'AMQPQueue' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castQueue'], 'AMQPExchange' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castExchange'], 'AMQPEnvelope' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\AmqpCaster', 'castEnvelope'], 'ArrayObject' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castArrayObject'], 'ArrayIterator' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castArrayIterator'], 'SplDoublyLinkedList' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castDoublyLinkedList'], 'SplFileInfo' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castFileInfo'], 'SplFileObject' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castFileObject'], 'SplHeap' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castHeap'], 'SplObjectStorage' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castObjectStorage'], 'SplPriorityQueue' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castHeap'], 'OuterIterator' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castOuterIterator'], 'WeakReference' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\SplCaster', 'castWeakReference'], 'Redis' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedis'], 'RedisArray' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedisArray'], 'RedisCluster' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\RedisCaster', 'castRedisCluster'], 'DateTimeInterface' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castDateTime'], 'DateInterval' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castInterval'], 'DateTimeZone' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castTimeZone'], 'DatePeriod' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DateCaster', 'castPeriod'], 'GMP' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\GmpCaster', 'castGmp'], 'MessageFormatter' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castMessageFormatter'], 'NumberFormatter' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castNumberFormatter'], 'IntlTimeZone' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlTimeZone'], 'IntlCalendar' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlCalendar'], 'IntlDateFormatter' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\IntlCaster', 'castIntlDateFormatter'], 'Memcached' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\MemcachedCaster', 'castMemcached'], 'ConfigTransformer20210601\\Ds\\Collection' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castCollection'], 'ConfigTransformer20210601\\Ds\\Map' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castMap'], 'ConfigTransformer20210601\\Ds\\Pair' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castPair'], 'ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DsPairStub' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\DsCaster', 'castPairStub'], 'CurlHandle' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castCurl'], ':curl' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castCurl'], ':dba' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castDba'], ':dba persistent' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castDba'], 'GdImage' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castGd'], ':gd' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castGd'], ':mysql link' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castMysqlLink'], ':pgsql large object' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLargeObject'], ':pgsql link' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLink'], ':pgsql link persistent' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castLink'], ':pgsql result' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\PgSqlCaster', 'castResult'], ':process' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castProcess'], ':stream' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStream'], 'OpenSSLCertificate' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castOpensslX509'], ':OpenSSL X.509' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castOpensslX509'], ':persistent stream' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStream'], ':stream-context' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\ResourceCaster', 'castStreamContext'], 'XmlParser' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\XmlResourceCaster', 'castXml'], ':xml' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\XmlResourceCaster', 'castXml'], 'RdKafka' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castRdKafka'], 'ConfigTransformer20210601\\RdKafka\\Conf' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castConf'], 'ConfigTransformer20210601\\RdKafka\\KafkaConsumer' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castKafkaConsumer'], 'ConfigTransformer20210601\\RdKafka\\Metadata\\Broker' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castBrokerMetadata'], 'ConfigTransformer20210601\\RdKafka\\Metadata\\Collection' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castCollectionMetadata'], 'ConfigTransformer20210601\\RdKafka\\Metadata\\Partition' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castPartitionMetadata'], 'ConfigTransformer20210601\\RdKafka\\Metadata\\Topic' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicMetadata'], 'ConfigTransformer20210601\\RdKafka\\Message' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castMessage'], 'ConfigTransformer20210601\\RdKafka\\Topic' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopic'], 'ConfigTransformer20210601\\RdKafka\\TopicPartition' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicPartition'], 'ConfigTransformer20210601\\RdKafka\\TopicConf' => ['ConfigTransformer20210601\\Symfony\\Component\\VarDumper\\Caster\\RdKafkaCaster', 'castTopicConf']];
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
            return new \ConfigTransformer20210601\Symfony\Component\VarDumper\Cloner\Data($this->doClone($var));
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
     */
    protected function castObject(\ConfigTransformer20210601\Symfony\Component\VarDumper\Cloner\Stub $stub, bool $isNested)
    {
        $obj = $stub->value;
        $class = $stub->class;
        if (\PHP_VERSION_ID < 80000 ? "\0" === ($class[15] ?? null) : \false !== \strpos($class, "@anonymous\0")) {
            $stub->class = \get_debug_type($obj);
        }
        if (isset($this->classInfo[$class])) {
            list($i, $parents, $hasDebugInfo, $fileInfo) = $this->classInfo[$class];
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
            $fileInfo = $r->isInternal() || $r->isSubclassOf(\ConfigTransformer20210601\Symfony\Component\VarDumper\Cloner\Stub::class) ? [] : ['file' => $r->getFileName(), 'line' => $r->getStartLine()];
            $this->classInfo[$class] = [$i, $parents, $hasDebugInfo, $fileInfo];
        }
        $stub->attr += $fileInfo;
        $a = \ConfigTransformer20210601\Symfony\Component\VarDumper\Caster\Caster::castObject($obj, $class, $hasDebugInfo, $stub->class);
        try {
            while ($i--) {
                if (!empty($this->casters[$p = $parents[$i]])) {
                    foreach ($this->casters[$p] as $callback) {
                        $a = $callback($obj, $a, $stub, $isNested, $this->filter);
                    }
                }
            }
        } catch (\Exception $e) {
            $a = [(\ConfigTransformer20210601\Symfony\Component\VarDumper\Cloner\Stub::TYPE_OBJECT === $stub->type ? \ConfigTransformer20210601\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL : '') . '⚠' => new \ConfigTransformer20210601\Symfony\Component\VarDumper\Exception\ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
    /**
     * Casts a resource to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     *
     * @return array The resource casted as array
     */
    protected function castResource(\ConfigTransformer20210601\Symfony\Component\VarDumper\Cloner\Stub $stub, bool $isNested)
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
            $a = [(\ConfigTransformer20210601\Symfony\Component\VarDumper\Cloner\Stub::TYPE_OBJECT === $stub->type ? \ConfigTransformer20210601\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL : '') . '⚠' => new \ConfigTransformer20210601\Symfony\Component\VarDumper\Exception\ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
}
