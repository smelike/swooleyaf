<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/8/24 0024
 * Time: 11:05
 */
namespace MessageQueue\Consumer;

use Constant\ErrorCode;
use Constant\Server;
use DesignPatterns\Factories\CacheSimpleFactory;
use DesignPatterns\Singletons\MysqlSingleton;
use DesignPatterns\Singletons\RedisSingleton;
use Exception\MessageQueue\MessageQueueException;
use Log\Log;
use Tool\Tool;

class RedisConsumer {
    /**
     * 管理缓存键名
     * @var string
     */
    private $managerKey = '';
    /**
     * 消费者列表
     * @var array
     */
    private $consumers = [];
    /**
     * 主题列表
     * @var array
     */
    private $topics = [];
    /**
     * 延续次数
     * @var int
     */
    private $continueTimes = 0;
    /**
     * 处理次数
     * @var int
     */
    private $handleTimes = 0;

    public function __construct() {
        $this->managerKey = Server::REDIS_PREFIX_MESSAGE_QUEUE . 'manager_redis';
        Log::setPath(\Yaconf::get('syserver.base.server.log_basepath') . 'sy/');
        $this->init();
    }

    private function __clone() {
    }

    /**
     * 初始化
     */
    private function init() {
        $this->continueTimes = 0;
        $this->topics = CacheSimpleFactory::getRedisInstance()->hGetAll($this->managerKey);
        $diffs = array_diff(array_keys($this->consumers), array_keys($this->topics));
        foreach ($diffs as $eDiff) {
            unset($this->consumers[$eDiff]);
        }
    }

    /**
     * 获取消费者
     * @param string $topic 主题
     * @return \MessageQueue\Consumer\RedisConsumerService
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
     * 处理数据
     */
    private function handleData() {
        foreach ($this->topics as $topic => $className) {
            $redisKey = Server::REDIS_PREFIX_MESSAGE_QUEUE . $topic;
            $dataList = CacheSimpleFactory::getRedisInstance()->lRange($redisKey, 0, 99);
            $dataNum = count($dataList);
            if($dataNum > 0){
                CacheSimpleFactory::getRedisInstance()->lTrim($redisKey, $dataNum, -1);
                $consumer = $this->getConsumer($topic);
                foreach ($dataList as $eData) {
                    $consumerData = Tool::jsonDecode($eData);
                    if(is_array($consumerData)){
                        $consumer->handleMessage($consumerData);
                    } else {
                        Log::error('主题为' . $topic . '的数据消费出错,消费数据为' . $eData);
                    }
                }
            }
        }
    }

    /**
     * 启动
     */
    public function start() {
        $this->handleData();
        $this->handleTimes++;
        $this->continueTimes++;
        RedisSingleton::getInstance()->reConnect();
        MysqlSingleton::getInstance()->reConnect();
        if($this->continueTimes >= 100){
            $this->init();
        }
    }
}