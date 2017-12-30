<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-8-23
 * Time: 下午10:05
 */
namespace MessageQueue\Consumer\Redis;

use Constant\Server;
use Log\Log;
use MessageQueue\Consumer\RedisConsumerService;
use MessageQueue\Consumer\RedisConsumerBase;
use Tool\Tool;

class AddLogService extends RedisConsumerBase implements RedisConsumerService {
    public function __construct() {
        parent::__construct();
        $this->topic = Server::MESSAGE_QUEUE_TOPIC_REDIS_ADD_LOG;
    }

    private function __clone() {
    }

    public function handleMessage(array $data) {
        Log::info('mqdata:' . Tool::jsonEncode($data, JSON_UNESCAPED_UNICODE));
    }
}