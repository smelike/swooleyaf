<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-3-18
 * Time: 下午11:11
 */
namespace Exception\Timer;

use Exception\BaseException;

class TimerException extends BaseException {
    public function __construct($message, $code) {
        parent::__construct($message, $code);
        $this->tipName = '定时器异常';
    }
}