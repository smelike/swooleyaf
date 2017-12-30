<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/9/9 0009
 * Time: 11:01
 */
namespace MessageQueue\Consumer\Redis;

use Constant\Server;
use Log\Log;
use MessageQueue\Consumer\RedisConsumerBase;
use MessageQueue\Consumer\RedisConsumerService;

class ReqHealthCheckService extends RedisConsumerBase implements RedisConsumerService {
    public function __construct() {
        parent::__construct();
        $this->topic = Server::MESSAGE_QUEUE_TOPIC_REDIS_REQ_HEALTH_CHECK;
    }

    private function __clone() {
    }

    public function handleMessage(array $data) {
        Log::warn('module:' . $data['module'] . ',uri:' . $data['uri'] . ' handle req cost more than ' . Server::SERVER_TIME_REQ_HEALTH_MIN . ' ms');
    }
}