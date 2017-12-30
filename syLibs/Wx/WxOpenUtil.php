<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-1
 * Time: 上午10:29
 */
namespace Wx;

use Constant\ErrorCode;
use Constant\Server;
use DesignPatterns\Factories\CacheSimpleFactory;
use DesignPatterns\Singletons\WxConfigSingleton;
use Exception\Wx\WxOpenException;
use Tool\Tool;
use Traits\SimpleTrait;

final class WxOpenUtil {
    use SimpleTrait;

    private static $urlComponentToken = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
    private static $urlAuthorizerToken = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=';
    private static $urlJsTicket = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=';
    private static $urlPreAuthCode = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=';
    private static $urlAuthUrl = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=';
    private static $urlAuthorizerAuth = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=';
    private static $urlSendCustom = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=';

    /**
     * 数组转xml
     * @param array $data
     * @return string
     * @throws WxOpenException
     */
    public static function arrayToXml(array $data) : string {
        if (count($data) == 0) {
            throw new WxOpenException('数组为空', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        $xml = '<xml>';
        foreach ($data as $key => $value) {
            if (is_numeric($value)) {
                $xml .= '<' . $key . '>' . $value . '</' . $key . '>';
            } else {
                $xml .= '<' . $key . '><![CDATA[' . $value . ']]></' . $key . '>';
            }
        }
        $xml .= '</xml>';

        return $xml;
    }

    /**
     * xml转数组
     * @param string $xml
     * @return array
     * @throws WxOpenException
     */
    public static function xmlToArray(string $xml) : array {
        if (strlen($xml . '') == 0) {
            throw new WxOpenException('xml数据异常', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $element = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $jsonStr = Tool::jsonEncode($element);
        return Tool::jsonDecode($jsonStr);
    }

    /**
     * 发送post请求
     * @param string $url 请求地址
     * @param string|array $data 数据
     * @param array $configs 配置数组
     * @return mixed
     * @throws WxOpenException
     */
    private static function sendPost(string $url, $data,array $configs=[]) {
        if (is_string($data)) {
            $dataStr = $data;
        } else if (is_array($data)) {
            if (isset($configs['data_type']) && ($configs['data_type'] == 'xml')) {
                $dataStr = self::arrayToXml($data);
            } else if (isset($configs['data_type']) && ($configs['data_type'] == 'json')) {
                $dataStr = Tool::jsonEncode($data, JSON_UNESCAPED_UNICODE);
            } else {
                $dataStr = http_build_query($data);
            }
        } else {
            throw new WxOpenException('数据格式不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        $timeout = (int)Tool::getArrayVal($configs, 'timeout', 2000);
        $headers = Tool::getArrayVal($configs, 'headers', false);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataStr);
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);

        //设置header
        curl_setopt($ch, CURLOPT_HEADER, $headers);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $resData = curl_exec($ch);
        $errorNo = curl_errno($ch);
        curl_close($ch);
        if ($errorNo == 0) {
            return $resData;
        } else {
            throw new WxOpenException('curl出错，错误码=' . $errorNo, ErrorCode::WXOPEN_POST_ERROR);
        }
    }

    /**
     * 发送get请求
     * @param string $url 请求地址
     * @param int $timeout 执行超时时间，默认2s
     * @return mixed
     * @throws WxOpenException
     */
    private static function sendGetReq(string $url,int $timeout=2000) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
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
            throw new WxOpenException('curl出错，错误码=' . $errorNo, ErrorCode::WXOPEN_GET_ERROR);
        }
    }

    /**
     * 获取平台access token
     * @param string $getType 获取类型 timer：通过微信服务器定时请求获取 inner：系统内部获取
     * @param string $verifyTicket
     * @return string
     * @throws WxOpenException
     */
    public static function getComponentAccessToken(string $getType,string $verifyTicket='') : string {
        $nowTime = time();
        $redis = CacheSimpleFactory::getRedisInstance();
        $expireTime = (int)$redis->hGet(Server::REDIS_PREFIX_WX_COMPONENT_ACCESS_TOKEN, 'expire');
        if (($nowTime + 600) < $expireTime) {
            return $redis->hGet(Server::REDIS_PREFIX_WX_COMPONENT_ACCESS_TOKEN, 'content');
        } else if ($getType != 'timer') {
            throw new WxOpenException('获取平台access token失败', ErrorCode::WXOPEN_POST_ERROR);
        }

        $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
        $resData = self::sendPost(self::$urlComponentToken, [
            'component_appid' => $openCommonConfig->getAppId(),
            'component_appsecret' => $openCommonConfig->getSecret(),
            'component_verify_ticket' => $verifyTicket,
        ], [
            'data_type' => 'json',
        ]);
        $resArr = Tool::jsonDecode($resData);
        if (isset($resArr['component_access_token'])) {
            $expireTime = $nowTime + $openCommonConfig->getExpireComponentAccessToken();
            $redis->hSet(Server::REDIS_PREFIX_WX_COMPONENT_ACCESS_TOKEN, 'content', $resArr['component_access_token']);
            $redis->hSet(Server::REDIS_PREFIX_WX_COMPONENT_ACCESS_TOKEN, 'expire', $expireTime . '');

            return $resArr['component_access_token'];
        } else {
            throw new WxOpenException('获取平台access token失败', ErrorCode::WXOPEN_POST_ERROR);
        }
    }

    /**
     * 获取授权者access token
     * @param string $appId 授权者微信号
     * @return string
     * @throws WxOpenException
     */
    public static function getAuthorizerAccessToken(string $appId) : string {
        $configs = WxConfigSingleton::getInstance()->getOpenAuthorizerConfigs($appId);
        if(empty($configs)){
            throw new WxOpenException('授权者不存在', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        $redis = CacheSimpleFactory::getRedisInstance();
        $redisKey = Server::REDIS_PREFIX_WX_AUTHORIZER_ACCESS_TOKEN . $appId;
        if ($redis->exists($redisKey)) {
            return $redis->get($redisKey);
        }

        $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
        $url = self::$urlAuthorizerToken . self::getComponentAccessToken(2);
        $resData = self::sendPost($url, [
            'component_appid' => $openCommonConfig->getAppId(),
            'authorizer_appid' => $configs['appid'],
            'authorizer_refresh_token' => $configs['refreshtoken'],
        ], [
            'data_type' => 'json',
        ]);
        $resArr = Tool::jsonDecode($resData);
        if (isset($resArr['authorizer_access_token'])) {
            $redis->set($redisKey, $resArr['authorizer_access_token'], (int)$openCommonConfig->getExpireAuthorizerAccessToken());

            return $resArr['authorizer_access_token'];
        } else {
            throw new WxOpenException('获取授权者access token失败', ErrorCode::WXOPEN_POST_ERROR);
        }
    }

    /**
     * 获取授权者jsapi ticket
     * @param string $appId 授权者微信号
     * @return string
     * @throws WxOpenException
     */
    public static function getJsTicket(string $appId) : string {
        $redis = CacheSimpleFactory::getRedisInstance();
        $redisKey = Server::REDIS_PREFIX_WX_AUTHORIZER_JS_TICKET . $appId;
        if ($redis->exists($redisKey)) {
            return $redis->get($redisKey);
        }

        $url = self::$urlJsTicket . self::getAuthorizerAccessToken($appId);
        $data = self::sendGetReq($url);
        $dataArr = Tool::jsonDecode($data);
        if ($dataArr['errcode'] == 0) {
            $redis->set($redisKey, $dataArr['ticket'], (int)WxConfigSingleton::getInstance()->getOpenCommonConfig()->getExpireAuthorizerJsTicket());

            return $dataArr['ticket'];
        } else {
            throw new WxOpenException($dataArr['errmsg'], ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    /**
     * 用SHA1算法生成安全签名
     * @param string $token 票据
     * @param string $timestamp 时间戳
     * @param string $nonce 随机字符串
     * @param string $encryptMsg 密文消息
     * @return string
     * @throws WxOpenException
     */
    private static function getSha1Val(string $token,string $timestamp,string $nonce,string $encryptMsg) : string {
        try {
            $saveArr = [$encryptMsg, $token, $timestamp, $nonce];
            sort($saveArr, SORT_STRING);
            $needStr = implode('', $saveArr);

            return sha1($needStr);
        } catch (\Exception $e) {
            throw new WxOpenException('生成安全签名出错', ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    /**
     * 填充补位需要加密的明文
     * @param string $text 需要加密的明文
     * @return string
     */
    private static function pkcs7Encode(string $text) : string {
        $blockSize = 32;
        $textLength = strlen($text);
        //计算需要填充的位数
        $addLength = $blockSize - ($textLength % $blockSize);
        if ($addLength == 0) {
            $addLength = $blockSize;
        }

        //获得补位所用的字符
        $needChr = chr($addLength);
        $tmp = '';
        for ($i = 0; $i < $addLength; $i++) {
            $tmp .= $needChr;
        }

        return $text . $tmp;
    }

    /**
     * 补位删除解密后的明文
     * @param string $text 解密后的明文
     * @return string
     */
    private static function pkcs7Decode(string $text) : string {
        $pad = ord(substr($text, -1));
        if (($pad < 1) || ($pad > 32)) {
            $pad = 0;
        }

        return substr($text, 0, (strlen($text) - $pad));
    }

    /**
     * 消息解密
     * @param string $encryptMsg 加密消息
     * @param string $appid 公众号appid
     * @param string $tag 标识 new：用新的aeskey解密 old：用旧的aeskey解密
     * @return array
     * @throws WxOpenException
     */
    private static function decrypt(string $encryptMsg,string $appid,string $tag='new') : array {
        $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
        if ($tag == 'new') {
            $aesKey = $openCommonConfig->getAesKeyNow();
            $key = base64_decode($aesKey . '=');
            $iv = substr($key, 0, 16);
        } else {
            $aesKey = $openCommonConfig->getAesKeyBefore();
            $key = base64_decode($aesKey . '=');
            $iv = substr($key, 0, 16);
        }

        $error = '';
        $xml = '';
        $decryptMsg = openssl_decrypt($encryptMsg, 'aes-256-cbc', substr($key, 0, 32), OPENSSL_ZERO_PADDING, $iv);
        $decodeMsg = self::pkcs7Decode($decryptMsg);
        if (strlen($decodeMsg) >= 16) {
            $msgContent = substr($decodeMsg, 16);
            $lengthList = unpack("N", substr($msgContent, 0, 4));
            $xml = substr($msgContent, 4, $lengthList[1]);
            $fromAppId = substr($msgContent, ($lengthList[1] + 4));
            if ($fromAppId != $appid) {
                $error = 'appid不匹配';
            }
        } else {
            $error = '解密失败';
        }

        if (strlen($error) > 0) {
            throw new WxOpenException($error, ErrorCode::WXOPEN_PARAM_ERROR);
        }

        return [
            'aes_key' => $aesKey,
            'content' => $xml,
        ];
    }

    /**
     * 密文解密
     * @param string $encryptXml 密文，对应POST请求的数据
     * @param string $appid 公众号appid
     * @param string $msgSignature 签名串，对应URL参数的msg_signature
     * @param string $nonceStr 随机串，对应URL参数的nonce
     * @param string $timestamp 时间戳 对应URL参数的timestamp
     * @return array
     * @throws WxOpenException
     */
    public static function decryptMsg(string $encryptXml,string $appid,string $msgSignature,string $nonceStr,string $timestamp='') : array {
        if ($timestamp) {
            $nowTime = $timestamp . '';
        } else {
            $nowTime = time() . '';
        }

        $signature = self::getSha1Val(WxConfigSingleton::getInstance()->getOpenCommonConfig()->getToken(), $nowTime, $nonceStr, $encryptXml);
        if ($signature != $msgSignature) {
            throw new WxOpenException('签名验证错误', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        try {
            //用当前的key校验密文
            $res = self::decrypt($encryptXml, $appid, 'new');
        } catch (\Exception $e) {
            //用上次的key校验密文
            $res = self::decrypt($encryptXml, $appid, 'old');
        }

        return $res;
    }

    /**
     * 消息加密
     * @param string $replyMsg 公众平台待回复用户的消息，xml格式的字符串
     * @param string $appid 公众号appid
     * @param string $aesKey 第三方平台的aes key
     * @param string $nonce 16位随机字符串
     * @return string
     */
    private static function encrypt(string $replyMsg,string $appid,string $aesKey,string $nonce) : string {
        $key = base64_decode($aesKey . '=');
        $iv = substr($key, 0, 16);

        //获得16位随机字符串，填充到明文之前
        $content1 = $nonce . pack("N", strlen($replyMsg)) . $replyMsg . $appid;
        $content2 = self::pkcs7Encode($content1);
        $encryptMsg = openssl_encrypt($content2, 'aes-256-cbc', substr($key, 0, 32), OPENSSL_ZERO_PADDING, $iv);
        return $encryptMsg;
    }

    /**
     * 明文加密
     * @param string $replyMsg 公众平台待回复用户的消息，xml格式的字符串
     * @param string $appid 公众号appid
     * @param string $aesKey 第三方平台的aes key
     * @return string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串
     */
    public static function encryptMsg(string $replyMsg,string $appid,string $aesKey) : string {
        $nonceStr = Tool::createNonceStr(16);
        $nowTime = time() . '';
        $encryptMsg = self::encrypt($replyMsg, $appid, $aesKey, $nonceStr);
        $signature = self::getSha1Val(WxConfigSingleton::getInstance()->getOpenCommonConfig()->getToken(), $nowTime, $nonceStr, $encryptMsg);
        $format = "<xml><Encrypt><![CDATA[%s]]></Encrypt><MsgSignature><![CDATA[%s]]></MsgSignature><TimeStamp>%s</TimeStamp><Nonce><![CDATA[%s]]></Nonce></xml>";

        return sprintf($format, $encryptMsg, $signature, $nowTime, $nonceStr);
    }

    /**
     * 获取授权页面
     * @return string
     */
    public static function getAuthUrl() : string {
        $authUrl = '';
        $openCommonConfig = WxConfigSingleton::getInstance()->getOpenCommonConfig();
        $url = self::$urlPreAuthCode . self::getComponentAccessToken('inner');
        $resData = self::sendPost($url, [
            'component_appid' => $openCommonConfig->getAppId(),
        ], [
            'data_type' => 'json',
        ]);
        $resArr = Tool::jsonDecode($resData);
        if (isset($resArr['pre_auth_code'])) {
            $authUrl = self::$urlAuthUrl . $openCommonConfig->getAppId()
                . '&pre_auth_code=' . $resArr['pre_auth_code']
                . '&redirect_uri=' . urlencode($openCommonConfig->getAuthUrlCallback());
        }

        return $authUrl;
    }

    /**
     * 获取授权者的授权信息
     * @param string $authCode 授权码
     * @return array
     */
    public static function getAuthorizerAuth(string $authCode) : array {
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlAuthorizerAuth . self::getComponentAccessToken('inner');
        $getRes = self::sendPost($url, [
            'component_appid' => WxConfigSingleton::getInstance()->getOpenCommonConfig()->getAppId(),
            'authorization_code' => $authCode,
        ], [
            'data_type' => 'json',
        ]);
        $getData = Tool::jsonDecode($getRes);
        if (isset($getData['authorization_info'])) {
            $resArr['data'] = $getData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = '获取授权信息失败';
        }

        return $resArr;
    }

    /**
     * 发送客服消息
     * @param array $data 消息数据
     * @param string $authNum 授权者编号
     * @return array
     */
    public static function sendCustomMsg(array $data,string $authNum) : array {
        $resArr = [
            'code' => 0,
        ];

        $url = self::$urlSendCustom . self::getAuthorizerAccessToken($authNum);
        $sendRes = self::sendPost($url, $data, [
            'data_type' => 'json',
            'headers' => [
                'Expect:',
            ]
        ]);
        $resData = Tool::jsonDecode($sendRes);
        if ($resData['errcode'] == 0) {
            $resArr['data'] = $resData;
        } else {
            $resArr['code'] = ErrorCode::WXOPEN_POST_ERROR;
            $resArr['message'] = $resData['errmsg'];
        }

        return $resArr;
    }
}