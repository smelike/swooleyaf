<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-8-26
 * Time: 上午12:21
 */
namespace MessageQueue\Consumer;

interface KafkaConsumerService {
    /**
     * 处理消息
     * @param array $data
     * @return mixed
     */
    public function handleMessage(array $data);
}