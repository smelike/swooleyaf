<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-8-23
 * Time: 下午9:42
 */
namespace MessageQueue\Consumer;

interface RedisConsumerService {
    /**
     * 处理消息
     * @param array $data
     * @return mixed
     */
    public function handleMessage(array $data);
}