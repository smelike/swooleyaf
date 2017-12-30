<?php
/**
 * 定时任务数据类
 * User: jw
 * Date: 17-7-28
 * Time: 下午8:58
 */
namespace Tool\Timer;

use Constant\ErrorCode;
use Exception\Timer\TimerException;
use Tool\Cron\CronTool;
use Tool\Tool;

class TimerData {
    const PERSIST_TYPE_NO = 0; //持久化类型-无
    const PERSIST_TYPE_INTERVAL = 1; //持久化类型-间隔时间
    const PERSIST_TYPE_CRON = 2; //持久化类型-cron计划
    /**
     * 定时任务ID
     * @var string
     */
    private $id = '';
    /**
     * 请求URI
     * @var string
     */
    private $uri = '';
    /**
     * 请求参数数组
     * @var array
     */
    private $params = [];
    /**
     * 时间
     * @var int|\Tool\Cron\CronData
     */
    private $time = 0;
    /**
     * 持久化, 0:否 1:间隔固定秒数 2:cron计划
     * @var int
     */
    private $persist = 0;

    public function __construct() {
        $this->id = Tool::createNonceStr(7) . str_replace('.', '', microtime(true));
        $this->persist = 0;
    }

    private function __clone() {
    }

    /**
     * @return string
     */
    public function getId() : string {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUri() : string {
        return $this->uri;
    }

    /**
     * @param string $uri
     * @throws \Exception\Timer\TimerException
     */
    public function setUri(string $uri) {
        if(preg_match('/^\S+$/', $uri) > 0){
            $this->uri = $uri;
        } else {
            throw new TimerException('请求URI格式不合法', ErrorCode::TIMER_PARAM_ERROR);
        }
    }

    /**
     * @return array
     */
    public function getParams() : array {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params) {
        $this->params = $params;
    }

    /**
     * @param int $persist 持久化类型 0:否 1:间隔固定秒数 2:cron计划
     * @param string|int $time 时间
     * @throws \Exception\Timer\TimerException
     */
    public function setExecTime(int $persist, $time) {
        if (!in_array($persist, [self::PERSIST_TYPE_NO, self::PERSIST_TYPE_INTERVAL, self::PERSIST_TYPE_CRON], true)) {
            throw new TimerException('持久化类型不合法', ErrorCode::TIMER_PARAM_ERROR);
        }

        $this->persist = $persist;
        if ($persist == self::PERSIST_TYPE_NO) {
            if (!is_int($time)) {
                throw new TimerException('执行时间必须是整数', ErrorCode::TIMER_PARAM_ERROR);
            } else if ($time <= time()) {
                throw new TimerException('执行时间必须大于当前时间', ErrorCode::TIMER_PARAM_ERROR);
            } else if($time > (time() + 31536000)){
                throw new TimerException('执行时间不能超过当前时间一年', ErrorCode::TIMER_PARAM_ERROR);
            }

            $this->time = $time;
        } else if ($persist == self::PERSIST_TYPE_INTERVAL) {
            if (!is_int($time)) {
                throw new TimerException('间隔时间必须是整数', ErrorCode::TIMER_PARAM_ERROR);
            } else if ($time <= 0) {
                throw new TimerException('间隔时间必须大于0', ErrorCode::TIMER_PARAM_ERROR);
            } else if ($time > 31536000) {
                throw new TimerException('间隔时间不能大于一年', ErrorCode::TIMER_PARAM_ERROR);
            }

            $this->time = $time;
        } else {
            if (!is_string($time)) {
                throw new TimerException('计划时间格式必须是字符串', ErrorCode::TIMER_PARAM_ERROR);
            }

            $this->time = CronTool::analyseCron($time);
        }
    }

    /**
     * @return int|\Tool\Cron\CronData
     */
    public function getTime() {
        return $this->time;
    }

    /**
     * @return int
     */
    public function getPersist() {
        return $this->persist;
    }
}