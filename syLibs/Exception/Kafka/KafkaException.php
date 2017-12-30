<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/6/9 0009
 * Time: 9:50
 */
namespace Exception\Kafka;

use Exception\BaseException;

class KafkaException extends BaseException {
    public function __construct($message, $code) {
        parent::__construct($message, $code);
        $this->tipName = 'Kafka异常';
    }
}