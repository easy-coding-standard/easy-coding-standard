<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\VarDumper\Caster;

use RdKafka\Conf;
use RdKafka\Exception as RdKafkaException;
use RdKafka\KafkaConsumer;
use RdKafka\Message;
use ECSPrefix20211002\RdKafka\Metadata\Broker as BrokerMetadata;
use RdKafka\Metadata\Collection as CollectionMetadata;
use RdKafka\Metadata\Partition as PartitionMetadata;
use RdKafka\Metadata\Topic as TopicMetadata;
use RdKafka\Topic;
use RdKafka\TopicConf;
use RdKafka\TopicPartition;
use ECSPrefix20211002\Symfony\Component\VarDumper\Cloner\Stub;
/**
 * Casts RdKafka related classes to array representation.
 *
 * @author Romain Neutron <imprec@gmail.com>
 */
class RdKafkaCaster
{
    /**
     * @param \RdKafka\KafkaConsumer $c
     * @param mixed[] $a
     * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castKafkaConsumer($c, $a, $stub, $isNested)
    {
        $prefix = \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        try {
            $assignment = $c->getAssignment();
        } catch (\RdKafka\Exception $e) {
            $assignment = [];
        }
        $a += [$prefix . 'subscription' => $c->getSubscription(), $prefix . 'assignment' => $assignment];
        $a += self::extractMetadata($c);
        return $a;
    }
    /**
     * @param \RdKafka\Topic $c
     * @param mixed[] $a
     * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castTopic($c, $a, $stub, $isNested)
    {
        $prefix = \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        $a += [$prefix . 'name' => $c->getName()];
        return $a;
    }
    /**
     * @param \RdKafka\TopicPartition $c
     * @param mixed[] $a
     */
    public static function castTopicPartition($c, $a)
    {
        $prefix = \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        $a += [$prefix . 'offset' => $c->getOffset(), $prefix . 'partition' => $c->getPartition(), $prefix . 'topic' => $c->getTopic()];
        return $a;
    }
    /**
     * @param \RdKafka\Message $c
     * @param mixed[] $a
     * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castMessage($c, $a, $stub, $isNested)
    {
        $prefix = \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        $a += [$prefix . 'errstr' => $c->errstr()];
        return $a;
    }
    /**
     * @param \RdKafka\Conf $c
     * @param mixed[] $a
     * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castConf($c, $a, $stub, $isNested)
    {
        $prefix = \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        foreach ($c->dump() as $key => $value) {
            $a[$prefix . $key] = $value;
        }
        return $a;
    }
    /**
     * @param \RdKafka\TopicConf $c
     * @param mixed[] $a
     * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castTopicConf($c, $a, $stub, $isNested)
    {
        $prefix = \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        foreach ($c->dump() as $key => $value) {
            $a[$prefix . $key] = $value;
        }
        return $a;
    }
    /**
     * @param \RdKafka $c
     * @param mixed[] $a
     * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castRdKafka($c, $a, $stub, $isNested)
    {
        $prefix = \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        $a += [$prefix . 'out_q_len' => $c->getOutQLen()];
        $a += self::extractMetadata($c);
        return $a;
    }
    /**
     * @param CollectionMetadata $c
     * @param mixed[] $a
     * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castCollectionMetadata($c, $a, $stub, $isNested)
    {
        $a += \iterator_to_array($c);
        return $a;
    }
    /**
     * @param TopicMetadata $c
     * @param mixed[] $a
     * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castTopicMetadata($c, $a, $stub, $isNested)
    {
        $prefix = \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        $a += [$prefix . 'name' => $c->getTopic(), $prefix . 'partitions' => $c->getPartitions()];
        return $a;
    }
    /**
     * @param PartitionMetadata $c
     * @param mixed[] $a
     * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castPartitionMetadata($c, $a, $stub, $isNested)
    {
        $prefix = \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        $a += [$prefix . 'id' => $c->getId(), $prefix . 'err' => $c->getErr(), $prefix . 'leader' => $c->getLeader()];
        return $a;
    }
    /**
     * @param BrokerMetadata $c
     * @param mixed[] $a
     * @param \Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castBrokerMetadata($c, $a, $stub, $isNested)
    {
        $prefix = \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        $a += [$prefix . 'id' => $c->getId(), $prefix . 'host' => $c->getHost(), $prefix . 'port' => $c->getPort()];
        return $a;
    }
    private static function extractMetadata($c)
    {
        $prefix = \ECSPrefix20211002\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        try {
            $m = $c->getMetadata(\true, null, 500);
        } catch (\RdKafka\Exception $e) {
            return [];
        }
        return [$prefix . 'orig_broker_id' => $m->getOrigBrokerId(), $prefix . 'orig_broker_name' => $m->getOrigBrokerName(), $prefix . 'brokers' => $m->getBrokers(), $prefix . 'topics' => $m->getTopics()];
    }
}
