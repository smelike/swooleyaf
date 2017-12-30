<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-16
 * Time: 2:08
 */
namespace AliDaYu;

use Constant\ErrorCode;
use DesignPatterns\Singletons\AliConfigSingleton;
use Log\Log;
use Tool\Tool;
use Traits\SimpleTrait;

final class DaYuUtil {
    use SimpleTrait;

    private static $urlHttp = 'http://gw.api.taobao.com/router/rest';

    /**
     * 生成短信签名字符串
     * @param array $data 参数数组
     * @return void
     */
    public static function createSmsSign(array &$data){
        $appSecret = AliConfigSingleton::getInstance()->getDaYuConfig()->getAppSecret();
        unset($data['sign']);
        ksort($data);
        $needStr = $appSecret;
        foreach ($data as $key => $value) {
            $needStr .= $key . $value;
        }
        $needStr .= $appSecret;
        $data['sign'] = strtoupper(md5($needStr));
    }

    /**
     * 发送短信
     * @param DaYuSmsSend $smsSend 短信对象
     * @return array
     */
    public static function sendSms(DaYuSmsSend $smsSend) : array {
        $resArr = [
            'code' => 0
        ];

        $reqData = $smsSend->getDetail();
        $res = self::sendPostReq(self::$urlHttp, $reqData);
        $resData = Tool::jsonDecode($res);
        if (isset($resData['alibaba_aliqin_fc_sms_num_send_response'])) {
            $resArr['data'] = $resData['alibaba_aliqin_fc_sms_num_send_response'];
        } else {
            $errorStr = Tool::jsonEncode($resData['error_response'], JSON_UNESCAPED_UNICODE);
            Log::error($errorStr, ErrorCode::ALIDAYU_POST_ERROR);
            $resArr['code'] = ErrorCode::ALIDAYU_POST_ERROR;
            $resArr['msg'] = $errorStr;
        }

        return $resArr;
    }

    /**
     * 查询短信
     * @param \AliDaYu\DaYuSmsQuery $smsQuery
     * @return array
     */
    public static function querySms(DaYuSmsQuery $smsQuery){
        $resArr = [
            'code' => 0
        ];

        $reqData = $smsQuery->getDetail();
        $res = self::sendPostReq(self::$urlHttp, $reqData);
        $resData = Tool::jsonDecode($res);
        if (isset($resData['alibaba_aliqin_fc_sms_num_query_response'])) {
            $resArr['data'] = $resData['alibaba_aliqin_fc_sms_num_query_response'];
        } else {
            $errorStr = Tool::jsonEncode($resData['error_response'], JSON_UNESCAPED_UNICODE);
            Log::error($errorStr, ErrorCode::ALIDAYU_POST_ERROR);
            $resArr['code'] = ErrorCode::ALIDAYU_POST_ERROR;
            $resArr['msg'] = $errorStr;
        }

        return $resArr;
    }

    /**
     * 发送POST请求
     * @param string $url 请求地址
     * @param array $data 请求参数
     * @param array $configs 配置数组
     *   timeout : int 毫秒时间
     * @return mixed
     */
    private static function sendPostReq(string $url,array $data,array $configs=[]){
        $timeout = isset($configs['timeout']) && is_numeric($configs['timeout']) ? (int)$configs['timeout'] : 1000;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Expect:',
        ]);
        $res = curl_exec($ch);
        $errorNo = curl_errno($ch);
        curl_close($ch);

        if($errorNo > 0){
            Log::error('短信请求失败,curl错误码为' . $errorNo, ErrorCode::ALIDAYU_POST_ERROR);
        }

        return $res;
    }
}