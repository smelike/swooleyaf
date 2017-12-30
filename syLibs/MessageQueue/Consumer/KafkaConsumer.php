<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-8-26
 * Time: 上午12:21
 */
namespace MessageQueue\Consumer;

use Constant\ErrorCode;
use Constant\Server;
use DesignPatterns\Factories\CacheSimpleFactory;
use Exception\Kafka\KafkaException;
use Exception\MessageQueue\MessageQueueException;
use Log\Log;
use RdKafka\Conf;
use RdKafka\TopicConf;
use Tool\Tool;

class KafkaConsumer {
    /**
     * @var \RdKafka\KafkaConsumer
     */
    private $kafkaConsumer = null;
    /**
     * @var \MessageQueue\Consumer\KafkaConsumer
     */
    private static $instance = null;
    /**
     * 初始化topic需处理的消息数量
     * @var int
     */
    private $msgInitNum = 0;
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
    /**
     * 消费者列表
     * @var array
     */
    private $consumers = [];

    private function __construct() {
        $configs = \Yaconf::get('kafka.' . SY_ENV);
        $groupId = Tool::getArrayVal($configs, 'group.id', '', true);
        if (!(is_string($groupId) && (strlen($groupId) > 0))) {
            throw new KafkaException('组ID必须是字符串且不能为空', ErrorCode::KAFKA_CONSUMER_ERROR);
        }

        $brokers = Tool::getArrayVal($configs, 'brokers', []);
        if (!(is_array($brokers) && (count($brokers) > 0))) {
            throw new KafkaException('broker不能为空', ErrorCode::KAFKA_CONSUMER_ERROR);
        }

        $conf = new Conf();
        $conf->set('group.id', $groupId);
        $conf->set('metadata.broker.list', implode(',', $brokers));

        $topicConf = new TopicConf();
        $topicConf->set('auto.offset.reset', Tool::getArrayVal($configs, 'offset.reset', 'smallest', true));
        $conf->setDefaultTopicConf($topicConf);

        $this->kafkaConsumer = new \RdKafka\KafkaConsumer($conf);
        $this->managerKey = Server::REDIS_PREFIX_MESSAGE_QUEUE . 'manager_kafka';
        $this->initTopics();
        Log::setPath(\Yaconf::get('syserver.base.server.log_basepath') . 'sy/');
    }

    private function __clone() {
    }

    /**
     * @return \MessageQueue\Consumer\KafkaConsumer
     */
    public static function getInstance() {
        if(is_null(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 初始化主题
     */
    private function initTopics() {
        $this->consumers = [];
        $this->topics = CacheSimpleFactory::getRedisInstance()->hGetAll($this->managerKey);
        $this->kafkaConsumer->subscribe(array_keys($this->topics));
    }

    /**
     * 获取消费者
     * @param string $topic
     * @return \MessageQueue\Consumer\KafkaConsumerService
     * @throws \Exception\MessageQueue\MessageQueueException
     */
    private function getConsumer(string $topic) {
        if(isset($this->consumers[$topic])){
            return $this->consumers[$topic];
        }
        if(!isset($this->topics[$topic])){
            throw new MessageQueueException('主题不存在', ErrorCode::MESSAGE_QUEUE_TOPIC_ERROR);
        }

        $className = $this->topics[$topic];
        $consumer = new $className();
        $this->consumers[$topic] = $consumer;

        return $consumer;
    }

    /**
     * 消费消息
     */
    public function consumeMsg() {
        $message = $this->kafkaConsumer->consume(120000);
        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                $this->msgInitNum++;
                $msgArr = Tool::jsonDecode($message->payload);
                $consumer = $this->getConsumer($msgArr['topic']);
                $consumer->handleMessage($msgArr['data']);
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                $this->msgInitNum++;
                Log::error('handle time out');
                break;
            default:
                $this->msgInitNum++;
                Log::error($message->errstr(), $message->err);
                break;
        }

        if($this->msgInitNum >= 5000){
            $this->initTopics();
            $this->msgInitNum = 0;
        }
    }
}