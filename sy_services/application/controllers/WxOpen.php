<?php
class WxOpenController extends CommonController {
    public function init() {
        parent::init();
    }

    /**
     * 处理微信服务器消息通知
     * @api {post} /WxOpen/handleWxNotify 处理微信服务器消息通知
     * @apiDescription 处理微信服务器消息通知
     * @apiGroup ServiceWxOpen
     * @apiParam {string} wx_xml 微信xml消息
     * @apiParam {string} nonce 随机字符串
     * @apiParam {string} msg_signature 消息签名
     * @apiParam {string} encrypt_type 加密方式
     * @apiParam {string} timestamp 时间戳
     * @apiSuccess WxOpenSuccess 请求失败
     * @apiSuccessExample success:
     *     success
     * @apiSuccess WxOpenFail 请求失败
     * @apiSuccessExample fail:
     *     fail
     * @SyFilter-{"field": "wx_xml","explain": "微信xml消息","type": "string","rules": {"required": 1,"min": 1}}
     * @SyFilter-{"field": "nonce","explain": "随机字符串","type": "string","rules": {"required": 1,"regex": "/^[a-zA-Z0-9]{1,32}$/"}}
     * @SyFilter-{"field": "msg_signature","explain": "消息签名","type": "string","rules": {"required": 1,"min": 1}}
     * @SyFilter-{"field": "encrypt_type","explain": "加密方式","type": "string","rules": {"required": 1,"regex": "/^aes$/"}}
     * @SyFilter-{"field": "timestamp","explain": "时间戳","type": "string","rules": {"required": 1,"regex": "/^[1-4]\d{9}$/"}}
     */
    public function handleWxNotifyAction() {
        $allParams = \Request\SyRequest::getParams();
        $incomeData = \Wx\WxOpenUtil::xmlToArray($allParams['wx_xml']);
        if (isset($incomeData['Encrypt']) && isset($incomeData['AppId'])) {
            $decryptRes = \Wx\WxOpenUtil::decryptMsg($incomeData['Encrypt'], \Wx\WxConfig::getOpenCommonConfigs('appid'), $allParams['msg_signature'], $allParams['nonce'], $allParams['timestamp']);
            $msgData = \Wx\WxOpenUtil::xmlToArray($decryptRes['content']);
            if($msgData['InfoType'] == 'component_verify_ticket') { //微信服务器定时监听
                \Wx\WxOpenUtil::getComponentAccessToken('timer', $msgData['ComponentVerifyTicket'] . '');
            } else if($msgData['InfoType'] == 'authorized'){ //授权
                $redis = \DesignPatterns\Factories\CacheSimpleFactory::getRedisInstance();
                $redisKey = \Constant\Server::REDIS_PREFIX_WX_AUTHORIZER_CONSTANT . $msgData['AuthorizerAppid'];
                $redis->hset($redisKey, 'authcode', $msgData['AuthorizationCode']);
                //获取授权信息
                $authInfo = \Wx\WxOpenUtil::getAuthorizerAuth($msgData['AuthorizationCode']);
                if($authInfo['code'] == 0){
                    $redis->hset($redisKey, 'refreshtoken', $authInfo['data']['authorization_info']['authorizer_refresh_token']);
                }
                //TODO：更新数据库授权信息
            } else if($msgData['InfoType'] == 'unauthorized'){ //取消授权
                $redis = \DesignPatterns\Factories\CacheSimpleFactory::getRedisInstance();
                $redisKey = \Constant\Server::REDIS_PREFIX_WX_AUTHORIZER_CONSTANT . $msgData['AuthorizerAppid'];
                $redis->del($redisKey);
                //TODO：更新数据库授权信息
            } else if($msgData['InfoType'] == 'updateauthorized'){ //更新授权
                $redis = \DesignPatterns\Factories\CacheSimpleFactory::getRedisInstance();
                $redisKey = \Constant\Server::REDIS_PREFIX_WX_AUTHORIZER_CONSTANT . $msgData['AuthorizerAppid'];
                $redis->hset($redisKey, 'authcode', $msgData['AuthorizationCode']);
                //获取授权信息
                $authInfo = \Wx\WxOpenUtil::getAuthorizerAuth($msgData['AuthorizationCode']);
                if($authInfo['code'] == 0){
                    $redis->hset($redisKey, 'refreshtoken', $authInfo['data']['authorization_info']['authorizer_refresh_token']);
                }
                //TODO：更新数据库授权信息
            }

            $this->SyResult->setData('success');
        } else {
            $this->SyResult->setData('fail');
        }

        $this->sendRsp();
    }

    /**
     * 处理授权者公众号消息
     * @api {post} /WxOpen/handleAuthorizerNotify 处理授权者公众号消息
     * @apiDescription 处理授权者公众号消息
     * @apiGroup ServiceWxOpen
     * @apiParam {string} wx_xml 微信xml消息
     * @apiParam {string} appid 授权者公众号id
     * @apiParam {string} openid 用户openid
     * @apiParam {string} nonce 随机字符串
     * @apiParam {string} msg_signature 消息签名
     * @apiParam {string} encrypt_type 加密方式
     * @apiParam {string} timestamp 时间戳
     * @apiSuccess WxOpenSuccess 请求失败
     * @apiSuccessExample success:
     *     <xml><ToUserName>fafasdf</ToUserName><Encrypt>dfdsfaf</Encrypt></xml>
     * @apiSuccess WxOpenFail 请求失败
     * @apiSuccessExample fail:
     *     fail
     * @SyFilter-{"field": "wx_xml","explain": "微信xml消息","type": "string","rules": {"required": 1,"min": 1}}
     * @SyFilter-{"field": "appid","explain": "授权者公众号id","type": "string","rules": {"required": 1,"min": 1}}
     * @SyFilter-{"field": "openid","explain": "用户openid","type": "string","rules": {"required": 1,"regex": "/^[0-9a-zA-Z\-\_]{28}$/"}}
     * @SyFilter-{"field": "nonce","explain": "随机字符串","type": "string","rules": {"required": 1,"regex": "/^[a-zA-Z0-9]{1,32}$/"}}
     * @SyFilter-{"field": "msg_signature","explain": "消息签名","type": "string","rules": {"required": 1,"min": 1}}
     * @SyFilter-{"field": "encrypt_type","explain": "加密方式","type": "string","rules": {"required": 1,"regex": "/^aes$/"}}
     * @SyFilter-{"field": "timestamp","explain": "时间戳","type": "string","rules": {"required": 1,"regex": "/^[1-4]\d{9}$/"}}
     */
    public function handleAuthorizerNotifyAction() {
        $returnStr = 'fail';
        $allParams = \Request\SyRequest::getParams();
        $incomeData = \Wx\WxOpenUtil::xmlToArray($allParams['wx_xml']);
        if (isset($incomeData['Encrypt']) && isset($incomeData['AppId'])) {
            $decryptRes = \Wx\WxOpenUtil::decryptMsg($incomeData['Encrypt'], \Wx\WxConfig::getOpenCommonConfigs('appid'), $allParams['msg_signature'], $allParams['nonce'], $allParams['timestamp']);
            $msgData = \Wx\WxOpenUtil::xmlToArray($decryptRes['content']);
            if(isset($msgData['MsgType'])){
                $saveArr = [];
                if($msgData['MsgType'] == 'event'){
                    $saveArr = [
                        'ToUserName' => $msgData['FromUserName'],
                        'FromUserName' => $msgData['ToUserName'],
                        'CreateTime' => $msgData['CreateTime'],
                        'MsgType' => 'text',
                        'Content' => $msgData['Event'] . 'from_callback',
                    ];
                } else if($msgData['MsgType'] == 'text'){
                    if($msgData['Content'] == 'TESTCOMPONENT_MSG_TYPE_TEXT'){
                        $saveArr = [
                            'ToUserName' => $msgData['FromUserName'],
                            'FromUserName' => $msgData['ToUserName'],
                            'CreateTime' => $msgData['CreateTime'],
                            'MsgType' => 'text',
                            'Content' => 'TESTCOMPONENT_MSG_TYPE_TEXT_callback',
                        ];
                    } else if(strpos($msgData['Content'], 'QUERY_AUTH_CODE:') === 0){ //全网开通专用
                        $authCode = str_replace('QUERY_AUTH_CODE:', '', $msgData['Content']);
                        //设置返回空消息
                        $saveArr = [
                            'ToUserName' => $msgData['FromUserName'],
                            'FromUserName' => $msgData['ToUserName'],
                            'CreateTime' => $msgData['CreateTime'],
                            'MsgType' => 'text',
                            'Content' => '',
                        ];

                        //使用授权码换取公众号的授权信息
                        $authInfo = \Wx\WxOpenUtil::getAuthorizerAuth($authCode);
                        //调用发送客服消息api回复文本消息
                        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $authInfo['data']['authorization_info']['authorizer_access_token'];
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                            'touser' => $msgData['FromUserName'],
                            'msgtype' => 'text',
                            'text' => [
                                'content' => $authCode . '_from_api',
                            ],
                        ], JSON_UNESCAPED_UNICODE));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Expect:',
                        ]);
                        curl_exec($ch);
                        curl_close($ch);
                    } else {
                        $saveArr = [
                            'ToUserName' => $msgData['FromUserName'],
                            'FromUserName' => $msgData['ToUserName'],
                            'CreateTime' => $msgData['CreateTime'],
                            'MsgType' => 'text',
                            'Content' => '',
                        ];
                    }
                }
                if(!empty($saveArr)){
                    $replyXml = \Wx\WxOpenUtil::arrayToXml($saveArr);
                    $returnStr = \Wx\WxOpenUtil::encryptMsg($replyXml, \Wx\WxConfig::getOpenCommonConfigs('appid'), $decryptRes['aes_key']);
                }
            }
        }

        $this->SyResult->setData($returnStr);
        $this->sendRsp();
    }

    /**
     * 获取开放平台授权地址
     * @api {get} /WxOpen/getComponentAuthUrl 获取开放平台授权地址
     * @apiDescription 获取开放平台授权地址
     * @apiGroup ServiceWxOpen
     * @apiUse CommonSuccess
     * @apiUse CommonFail
     */
    public function getComponentAuthUrlAction() {
        $authUrl = \Wx\WxOpenUtil::getAuthUrl();
        if(strlen($authUrl) > 0){
            $this->SyResult->setData([
                'url' => $authUrl,
            ]);
        } else {
            $this->SyResult->setCodeMsg(\Constant\ErrorCode::COMMON_PARAM_ERROR, '获取授权地址失败');
        }

        $this->sendRsp();
    }
}