<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-7-28
 * Time: 下午9:00
 */
namespace Tool\Timer;

use Constant\ErrorCode;
use Constant\Server;
use DesignPatterns\Factories\CacheSimpleFactory;
use Exception\Timer\TimerException;
use Tool\Tool;
use Traits\SimpleTrait;

class TimerTool {
    use SimpleTrait;

    const TASK_SINGLE = 'singles';
    const TASK_PERSIST = 'persists';

    /**
     * 添加任务
     * @param \Tool\Timer\TimerData $timerData
     * @return array
     * @throws \Exception\Timer\TimerException
     */
    public static function addTimer(TimerData $timerData){
        if (strlen($timerData->getUri()) == 0) {
            throw new TimerException('请求URI不能为空', ErrorCode::TIMER_PARAM_ERROR);
        } else if (is_int($timerData->getTime()) && ($timerData->getTime() == 0)) {
            throw new TimerException('时间不能为空', ErrorCode::TIMER_PARAM_ERROR);
        }

        if ($timerData->getPersist() == TimerData::PERSIST_TYPE_NO) {
            CacheSimpleFactory::getRedisInstance()->hMset(Server::REDIS_PREFIX_TIMER . $timerData->getId(), [
                'id' => $timerData->getId(),
                'persist' => $timerData->getPersist(),
                'uri' => $timerData->getUri(),
                'params' => Tool::pack($timerData->getParams()),
                'time' => $timerData->getTime(),
            ]);
            CacheSimpleFactory::getRedisInstance()->sAdd(Server::REDIS_PREFIX_TIMER . self::TASK_SINGLE, $timerData->getTime());
            CacheSimpleFactory::getRedisInstance()->rPush(Server::REDIS_PREFIX_TIMER . $timerData->getTime(), $timerData->getId());
        } else if ($timerData->getPersist() == TimerData::PERSIST_TYPE_INTERVAL) {
            CacheSimpleFactory::getRedisInstance()->hMset(Server::REDIS_PREFIX_TIMER . $timerData->getId(), [
                'id' => $timerData->getId(),
                'persist' => $timerData->getPersist(),
                'uri' => $timerData->getUri(),
                'params' => Tool::pack($timerData->getParams()),
                'time' => $timerData->getTime(),
            ]);
            CacheSimpleFactory::getRedisInstance()->sAdd(Server::REDIS_PREFIX_TIMER . self::TASK_PERSIST, $timerData->getId());
        } else {
            CacheSimpleFactory::getRedisInstance()->hMset(Server::REDIS_PREFIX_TIMER . $timerData->getId(), [
                'id' => $timerData->getId(),
                'persist' => $timerData->getPersist(),
                'uri' => $timerData->getUri(),
                'params' => Tool::pack($timerData->getParams()),
                'cron' => $timerData->getTime()->getCron(),
                'cron_seconds' => Tool::pack($timerData->getTime()->getSeconds()),
                'cron_minutes' => Tool::pack($timerData->getTime()->getMinutes()),
                'cron_hours' => Tool::pack($timerData->getTime()->getHours()),
                'cron_days' => Tool::pack($timerData->getTime()->getDays()),
                'cron_months' => Tool::pack($timerData->getTime()->getMonths()),
                'cron_weeks' => Tool::pack($timerData->getTime()->getWeeks()),
            ]);
            CacheSimpleFactory::getRedisInstance()->sAdd(Server::REDIS_PREFIX_TIMER . self::TASK_PERSIST, $timerData->getId());
        }

        return [
            'timer_id' => $timerData->getId(),
        ];
    }

    /**
     * 删除任务
     * @param string $timerId 任务ID
     * @return int
     */
    public static function deleteTimer(string $timerId){
        $delNum = CacheSimpleFactory::getRedisInstance()->del(Server::REDIS_PREFIX_TIMER . $timerId);
        CacheSimpleFactory::getRedisInstance()->sRem(Server::REDIS_PREFIX_TIMER . self::TASK_PERSIST, $timerId);

        return $delNum;
    }

    /**
     * 刷新任务,将超时未处理的任务延期到10s后
     */
    public static function refreshTimer(){
        $num = 0;
        $time1 = time() + 3;
        $time2 = time() + 10;
        $redisKey1 = Server::REDIS_PREFIX_TIMER . $time2;
        $redisKey2 = Server::REDIS_PREFIX_TIMER . self::TASK_SINGLE;
        $times = CacheSimpleFactory::getRedisInstance()->sMembers($redisKey2);
        sort($times);

        foreach ($times as $eTime) {
            if($eTime <= $time1){
                $redisKey3 = Server::REDIS_PREFIX_TIMER . $eTime;
                $data = CacheSimpleFactory::getRedisInstance()->lPop($redisKey3);
                $dataArr = [];
                while ($data !== false) {
                    $num++;
                    $dataArr[] = $data;
                    $data = CacheSimpleFactory::getRedisInstance()->lPop($redisKey3);
                }

                $saveArr = [];
                foreach ($dataArr as $eData) {
                    $saveArr[] = $eData;
                    if(count($saveArr) == 10){
                        CacheSimpleFactory::getRedisInstance()->rPush($redisKey1, $saveArr[0], $saveArr[1], $saveArr[2], $saveArr[3], $saveArr[4], $saveArr[5], $saveArr[6], $saveArr[7], $saveArr[8], $saveArr[9]);
                        $saveArr = [];
                    }
                }
                if(!empty($saveArr)){
                    foreach ($saveArr as $eSave) {
                        CacheSimpleFactory::getRedisInstance()->rPush($redisKey1, $eSave);
                    }
                }

                CacheSimpleFactory::getRedisInstance()->sRem($redisKey2, $eTime);
            } else {
                break;
            }
        }

        if ($num > 0) {
            CacheSimpleFactory::getRedisInstance()->sAdd($redisKey2, $time2);
        }
    }
}