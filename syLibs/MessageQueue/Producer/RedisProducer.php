<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-8-23
 * Time: 下午11:26
 */
namespace MessageQueue\Producer;

use Constant\ErrorCode;
use Constant\Server;
use DesignPatterns\Factories\CacheSimpleFactory;
use Exception\MessageQueue\MessageQueueException;
use MessageQueue\Consumer\RedisConsumerService;
use Tool\Tool;

class RedisProducer {
    /**
     * @var \MessageQueue\Producer\RedisProducer
     */
    private static $instance = null;
    /**
     * 管理缓存键名
     * @var string
     */
    private $managerKey = '';
    /**
     * 允许的主题数组
     * @var array
     */
    private $topics = [];

    private function __construct() {
        $this->managerKey = Server::REDIS_PREFIX_MESSAGE_QUEUE . 'manager_redis';
        $this->topics = [
            Server::MESSAGE_QUEUE_TOPIC_REDIS_ADD_LOG,
            Server::MESSAGE_QUEUE_TOPIC_REDIS_REQ_HEALTH_CHECK,
        ];
    }

    private function __clone() {
    }

    /**
     * @return \MessageQueue\Producer\RedisProducer
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 检测主题是否合法
     * @param string $topic 主题
     * @throws \Exception\MessageQueue\MessageQueueException
     */
    private function checkTopic(string $topic) {
        if (!in_array($topic, $this->topics)) {
            throw new MessageQueueException('主题不支持', ErrorCode::MESSAGE_QUEUE_TOPIC_ERROR);
        }
    }

    /**
     * 添加消费者
     * @param string $topic 主题
     * @param \MessageQueue\Consumer\RedisConsumerService $consumer 生产者对象
     * @return bool
     * @throws \Exception\MessageQueue\MessageQueueException
     */
    public function addConsumer(string $topic,RedisConsumerService $consumer) {
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

    /**
     * 添加主题数据
     * @param string $topic
     * @param array $data
     * @return bool|int
     */
    public function addTopicData(string $topic,array $data) {
        $this->checkTopic($topic);
        
        return CacheSimpleFactory::getRedisInstance()->rPush(Server::REDIS_PREFIX_MESSAGE_QUEUE . $topic, Tool::jsonEncode($data, JSON_UNESCAPED_UNICODE));
    }
}