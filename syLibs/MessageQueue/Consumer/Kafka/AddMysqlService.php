<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-8-25
 * Time: 下午11:34
 */
namespace MessageQueue\Consumer\Kafka;

use Constant\Server;
use Log\Log;
use MessageQueue\Consumer\KafkaConsumerBase;
use MessageQueue\Consumer\KafkaConsumerService;

class AddMysqlService extends KafkaConsumerBase implements KafkaConsumerService {
    public function __construct() {
        parent::__construct();
        $this->topic = Server::MESSAGE_QUEUE_TOPIC_KAFKA_ADD_MYSQL_LOG;
    }

    private function __clone() {
    }

    public function handleMessage(array $data) {
        Log::info('kafka msg:' . print_r($data, true));
    }
}