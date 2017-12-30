<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-8-26
 * Time: 上午12:21
 */
namespace MessageQueue\Consumer;

abstract class KafkaConsumerBase {
    /**
     * 主题
     * @var string
     */
    public $topic = '';

    public function __construct() {
    }
}