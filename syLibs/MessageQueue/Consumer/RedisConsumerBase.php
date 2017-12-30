<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-8-23
 * Time: 下午9:44
 */
namespace MessageQueue\Consumer;

abstract class RedisConsumerBase {
    /**
     * 主题
     * @var string
     */
    public $topic = '';

    public function __construct() {
    }
}