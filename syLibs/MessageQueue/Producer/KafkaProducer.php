<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-8-25
 * Time: 下午11:09
 */
namespace MessageQueue\Producer;

use Constant\ErrorCode;
use Constant\Server;
use DesignPatterns\Factories\CacheSimpleFactory;
use Exception\Kafka\KafkaException;
use Exception\MessageQueue\MessageQueueException;
use MessageQueue\Consumer\KafkaConsumerService;
use RdKafka\Producer;
use Tool\Tool;

class KafkaProducer {
    /**
     * @var \RdKafka\Producer
     */
    private $producer = null;
    /**
     * @var \MessageQueue\Producer\KafkaProducer
     */
    private static $instance = null;
    /**
     * 管理缓存键名
     * @var string
     */
    private $managerKey = '';
    /**
     * 主题列表
     * @var array
     */
    private $topics = [];

    private function __construct() {
        $configs = \Yaconf::get('kafka.' . SY_ENV);
        $brokers = Tool::getArrayVal($configs, 'brokers', []);
        if (is_array($brokers) && !empty($brokers)) {
            $this->producer = new Producer();
            $this->producer->setLogLevel(LOG_DEBUG);
            $this->producer->addBrokers(implode(',', $brokers));
        } else {
            throw new KafkaException('broker不能为空', ErrorCode::KAFKA_PRODUCER_ERROR);
        }

        $this->managerKey = Server::REDIS_PREFIX_MESSAGE_QUEUE . 'manager_kafka';
        $this->topics = [
            Server::MESSAGE_QUEUE_TOPIC_KAFKA_ADD_MYSQL_LOG,
        ];
    }

    private function __clone() {
    }

    /**
     * @return \MessageQueue\Producer\KafkaProducer
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $topic 主题名称
     * @throws \Exception\Kafka\KafkaException
     */
    private function checkTopic(string $topicName) {
        if (!in_array($topicName, $this->topics)) {
            throw new KafkaException('主题不支持', ErrorCode::KAFKA_PRODUCER_ERROR);
        }
    }

    /**
     * 发送kafka数据
     * @param string $topicName 主题名称
     * @param array $data
     */
    public function sendData(string $topicName,array $data) {
        $this->checkTopic($topicName);
        $topic = $this->producer->newTopic($topicName);
        foreach ($data as $eData) {
            $topic->produce(RD_KAFKA_PARTITION_UA, 0, Tool::jsonEncode([
                'topic' => $topicName,
                'data' => $eData,
            ], JSON_UNESCAPED_UNICODE));
            $this->producer->poll(0);
        }

        while ($this->producer->getOutQLen() > 0) {
            $this->producer->poll(50);
        }
    }

    /**
     * 添加消费者
     * @param string $topic 主题
     * @param \MessageQueue\Consumer\KafkaConsumerService $consumer
     * @return bool
     * @throws \Exception\MessageQueue\MessageQueueException
     */
    public function addConsumer(string $topic,KafkaConsumerService $consumer) {
        $this->checkTopic($topic);
        if (CacheSimpleFactory::getRedisInstance()->hGet($this->managerKey, $topic) === false) {
            if($consumer->topic != $topic){
                throw new MessageQueueException('主题和生产者主题不一致', ErrorCode::MESSAGE_QUEUE_TOPIC_ERROR);
            }
            if(CacheSimpleFactory::getRedisInstance()->hSet($this->managerKey, $topic, '\\' . get_class($consumer)) === false){
                throw new MessageQueueException('添加主题失败', ErrorCode::MESSAGE_QUEUE_TOPIC_ERROR);
            }
        }

        return true;
    }

    /**
     * 删除消费者
     * @param string $topic
     * @return bool|int
     */
    public function deleteConsumer(string $topic) {
        $this->checkTopic($topic);

        return CacheSimpleFactory::getRedisInstance()->hDel($this->managerKey, $topic);
    }
}