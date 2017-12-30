<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-7-30
 * Time: 上午9:34
 */
namespace Tool\Timer;

use Constant\ErrorCode;
use Constant\Server;
use DesignPatterns\Factories\CacheSimpleFactory;
use Exception\Timer\TimerException;
use Log\Log;
use Request\RequestSign;
use Tool\Cron\CronData;
use Tool\Tool;

class SyTimer {
    /**
     * 请求域名
     * @var string
     */
    private $apiDomain = '';
    /**
     * 当前时间戳
     * @var int
     */
    private $nowTime = 0;
    /**
     * 执行次数
     * @var int
     */
    private $execTimes = 0;
    /**
     * 持久化任务列表
     * @var array
     */
    private $persists = [];

    public function __construct(string $apiDomain) {
        if (preg_match('/^(http|https)\:\/\/\S+$/', $apiDomain) == 0) {
            throw new TimerException('api域名不合法', ErrorCode::TIMER_PARAM_ERROR);
        }

        Log::setPath(\Yaconf::get('syserver.base.server.log_basepath') . 'sy/');
        $this->apiDomain = $apiDomain;
        $this->nowTime = time();
    }

    private function __clone() {
    }

    /**
     * @param string $url
     * @return mixed
     */
    private function sendReq(string $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $errorNo = curl_errno($ch);
        curl_close($ch);
        if ($errorNo == 0) {
            return $data;
        } else {
            Log::error('exec timer curl出错，错误码=' . $errorNo, ErrorCode::TIMER_GET_ERROR);

            return null;
        }
    }

    /**
     * @param array $taskData
     */
    private function execTask(array $taskData) {
        $url = $this->apiDomain . $taskData['uri'];
        if (!empty($taskData['params'])) {
            $url .= '?' . http_build_query($taskData['params']);
        }
        RequestSign::makeSignUrl($url);
        $sendRes = $this->sendReq($url);
        if ($sendRes !== null) {
            $resData = Tool::jsonDecode($sendRes);
            if(is_array($resData)){
                Log::info('exec timer: ' . Tool::jsonEncode($resData, JSON_UNESCAPED_UNICODE));
            } else {
                Log::info('exec timer: ' . $sendRes);
            }
        }
    }

    /**
     * 处理单次任务
     * @param int $timestamp 时间戳
     */
    private function handleSingleTask(int $timestamp) {
        $redisKey = Server::REDIS_PREFIX_TIMER . $timestamp;
        if (CacheSimpleFactory::getRedisInstance()->lLen($redisKey) > 0) {
            $taskId = CacheSimpleFactory::getRedisInstance()->lPop($redisKey);
            while ($taskId) {
                $taskData = CacheSimpleFactory::getRedisInstance()->hGetAll(Server::REDIS_PREFIX_TIMER . $taskId);
                if($taskData) {
                    $this->execTask([
                        'uri' => $taskData['uri'],
                        'params' => Tool::unpack($taskData['params']),
                    ]);
                    CacheSimpleFactory::getRedisInstance()->del(Server::REDIS_PREFIX_TIMER . $taskId);
                }

                $taskId = CacheSimpleFactory::getRedisInstance()->lPop($redisKey);
            }

            CacheSimpleFactory::getRedisInstance()->sRem(Server::REDIS_PREFIX_TIMER . 'singles', $timestamp);
        }
    }

    /**
     * 刷新持久化任务列表
     * @param int $timestamp
     */
    private function refreshPersists(int $timestamp) {
        if (($timestamp % 30) == 0) {
            $this->persists = [];
            $existList = CacheSimpleFactory::getRedisInstance()->sMembers(Server::REDIS_PREFIX_TIMER . 'persists');
            foreach ($existList as $timerId) {
                $timerData = CacheSimpleFactory::getRedisInstance()->hGetAll(Server::REDIS_PREFIX_TIMER . $timerId);
                if ($timerData) {
                    $this->persists[$timerData['id']] = [
                        'id' => $timerData['id'],
                        'persist' => (int)$timerData['persist'],
                        'uri' => $timerData['uri'],
                        'params' => Tool::unpack($timerData['params']),
                    ];
                    if ($timerData['persist'] == TimerData::PERSIST_TYPE_INTERVAL) {
                        $this->persists[$timerData['id']]['time'] = (int)$timerData['time'];
                    } else {
                        $cronData = new CronData();
                        $cronData->setCron($timerData['cron']);
                        $cronData->setSeconds(Tool::unpack($timerData['cron_seconds']));
                        $cronData->setMinutes(Tool::unpack($timerData['cron_minutes']));
                        $cronData->setHours(Tool::unpack($timerData['cron_hours']));
                        $cronData->setDays(Tool::unpack($timerData['cron_days']));
                        $cronData->setMonths(Tool::unpack($timerData['cron_months']));
                        $cronData->setWeeks(Tool::unpack($timerData['cron_weeks']));
                        $this->persists[$timerData['id']]['time'] = $cronData;
                    }
                }
            }
        }
    }

    /**
     * 处理持久任务
     * @param int $timestamp 时间戳
     */
    private function handlePersistTask(int $timestamp) {
        $this->refreshPersists($timestamp);
        foreach ($this->persists as $persist) {
            if (($persist['persist'] == TimerData::PERSIST_TYPE_INTERVAL) && (($timestamp % $persist['time']) == 0)) {
                $this->execTask([
                    'uri' => $persist['uri'],
                    'params' => $persist['params'],
                ]);
            } else if (($persist['persist'] == TimerData::PERSIST_TYPE_CRON) && $persist['time']->checkTime($timestamp)) {
                $this->execTask([
                    'uri' => $persist['uri'],
                    'params' => $persist['params'],
                ]);
            }
        }
    }


    /**
     * 处理任务
     */
    public function handleTask() {
        $this->handleSingleTask($this->nowTime);
        $this->handlePersistTask($this->nowTime);

        $this->execTimes++;
        if(($this->execTimes % 1000) != 0){
            $this->nowTime++;
        } else { //每执行1000次重置下当前时间戳,避免任务执行越久,和系统时钟误差越大
            $this->nowTime = time();
        }
    }
}